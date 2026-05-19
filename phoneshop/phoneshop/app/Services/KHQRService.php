<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KHQRService
{
    protected string $apiUrl;
    protected string $token;
    protected array $merchant;

    public function __construct()
    {
        $this->apiUrl = config('services.bakong.api_url') ?? 'https://api-bakong.nbc.gov.kh';
        $this->token = config('services.bakong.token') ?? '';
        $this->merchant = config('services.bakong.merchant') ?? [];
    }

    /**
     * Generate Individual KHQR String locally
     */
    public function generateIndividualQR(array $data): array
    {
        try {
            $qrString = $this->buildKHQRString($data);
            $md5 = md5($qrString);

            return [
                'data' => [
                    'qr' => $qrString,
                    'md5' => $md5,
                ],
                'status' => ['code' => 0]
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generate Merchant KHQR
     */
    public function generateMerchantQR(array $data): array
    {
        return $this->generateIndividualQR($data);
    }

    /**
     * Build KHQR String according to EMV QR Code specification
     */
    protected function buildKHQRString(array $data): string
    {
        $bakongId = $data['bakong_account_id'] ?? $this->merchant['bakong_id'] ?? '';
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'USD';
        $merchantName = $data['merchant_name'] ?? $this->merchant['name'] ?? 'Merchant';
        $merchantCity = $data['merchant_city'] ?? $this->merchant['city'] ?? 'PHNOM PENH';
        $billNumber = $data['bill_number'] ?? '';
        $mobileNumber = $data['mobile_number'] ?? '';
        $storeLabel = $data['store_label'] ?? '';
        $terminalLabel = $data['terminal_label'] ?? '';

        // Build QR data
        $qr = '';
        
        // Payload Format Indicator (ID 00)
        $qr .= $this->tlv('00', '01');
        
        // Point of Initiation Method (ID 01) - 12 = Dynamic QR
        $qr .= $this->tlv('01', '12');
        
        // Merchant Account Information (ID 29 for Bakong)
        $merchantAccount = $this->tlv('00', $bakongId);
        if (!empty($mobileNumber)) {
            $merchantAccount .= $this->tlv('01', $mobileNumber);
        }
        $qr .= $this->tlv('29', $merchantAccount);
        
        // Merchant Category Code (ID 52)
        $qr .= $this->tlv('52', '5999');
        
        // Transaction Currency (ID 53) - 840 = USD, 116 = KHR
        $currencyCode = $currency === 'KHR' ? '116' : '840';
        $qr .= $this->tlv('53', $currencyCode);
        
        // Transaction Amount (ID 54)
        if ($amount > 0) {
            $qr .= $this->tlv('54', number_format($amount, 2, '.', ''));
        }
        
        // Country Code (ID 58)
        $qr .= $this->tlv('58', 'KH');
        
        // Merchant Name (ID 59)
        $qr .= $this->tlv('59', substr($merchantName, 0, 25));
        
        // Merchant City (ID 60)
        $qr .= $this->tlv('60', substr($merchantCity, 0, 15));
        
        // Additional Data Field (ID 62)
        $additionalData = '';
        if (!empty($billNumber)) {
            $additionalData .= $this->tlv('01', $billNumber);
        }
        if (!empty($mobileNumber)) {
            $additionalData .= $this->tlv('02', $mobileNumber);
        }
        if (!empty($storeLabel)) {
            $additionalData .= $this->tlv('03', $storeLabel);
        }
        if (!empty($terminalLabel)) {
            $additionalData .= $this->tlv('07', $terminalLabel);
        }
        if (!empty($additionalData)) {
            $qr .= $this->tlv('62', $additionalData);
        }
        
        // Timestamp (ID 99)
        $timestamp = $this->tlv('00', (string)round(microtime(true) * 1000));
        $qr .= $this->tlv('99', $timestamp);
        
        // CRC (ID 63) - placeholder, will be calculated
        $qr .= '6304';
        
        // Calculate CRC16
        $crc = $this->crc16($qr);
        $qr = substr($qr, 0, -4) . '6304' . strtoupper(dechex($crc));
        
        return $qr;
    }

    /**
     * Create TLV (Tag-Length-Value) string
     */
    protected function tlv(string $tag, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $length . $value;
    }

    /**
     * Calculate CRC16 CCITT-FALSE
     */
    protected function crc16(string $data): int
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $crc;
    }

    /**
     * Check Payment Status via Bakong API
     */
    public function checkPayment(string $md5): array
    {
        try {
            if (empty($this->token)) {
                return ['error' => 'Bakong token not configured'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/v1/check_transaction_by_md5', [
                'md5' => $md5,
            ]);

            return $response->json() ?? ['error' => 'No response'];
        } catch (\Exception $e) {
            Log::error('KHQR checkPayment error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Verify QR Code
     */
    public function verifyQR(string $qrCode): array
    {
        try {
            if (empty($this->token)) {
                return ['data' => ['valid' => false, 'error' => 'Bakong token not configured']];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/v1/verify_qr', [
                'qr' => $qrCode,
            ]);

            $result = $response->json();
            
            return [
                'data' => [
                    'valid' => isset($result['responseCode']) && $result['responseCode'] === 0,
                    'details' => $result,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('KHQR verifyQR error: ' . $e->getMessage());
            return ['data' => ['valid' => false, 'error' => $e->getMessage()]];
        }
    }

    /**
     * Decode QR Code
     */
    public function decodeQR(string $qrCode): array
    {
        try {
            $result = $this->parseKHQR($qrCode);
            
            return [
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('KHQR decodeQR error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Parse KHQR string to extract data
     */
    protected function parseKHQR(string $qrString): array
    {
        $result = [];
        $i = 0;
        
        while ($i < strlen($qrString)) {
            if ($i + 2 > strlen($qrString)) break;
            
            $tag = substr($qrString, $i, 2);
            $i += 2;
            
            if ($i + 2 > strlen($qrString)) break;
            
            $length = (int)substr($qrString, $i, 2);
            $i += 2;
            
            if ($i + $length > strlen($qrString)) break;
            
            $value = substr($qrString, $i, $length);
            $i += $length;
            
            // Parse known tags
            switch ($tag) {
                case '00':
                    $result['format_indicator'] = $value;
                    break;
                case '01':
                    $result['initiation_method'] = $value;
                    break;
                case '29':
                    $result['merchant_account'] = $this->parseNestedTLV($value);
                    break;
                case '52':
                    $result['merchant_category_code'] = $value;
                    break;
                case '53':
                    $result['currency'] = $value;
                    break;
                case '54':
                    $result['amount'] = $value;
                    break;
                case '58':
                    $result['country_code'] = $value;
                    break;
                case '59':
                    $result['merchant_name'] = $value;
                    break;
                case '60':
                    $result['merchant_city'] = $value;
                    break;
                case '62':
                    $result['additional_data'] = $this->parseNestedTLV($value);
                    break;
                case '63':
                    $result['crc'] = $value;
                    break;
            }
        }
        
        return $result;
    }

    /**
     * Parse nested TLV data
     */
    protected function parseNestedTLV(string $data): array
    {
        $result = [];
        $i = 0;
        
        while ($i < strlen($data)) {
            if ($i + 2 > strlen($data)) break;
            
            $tag = substr($data, $i, 2);
            $i += 2;
            
            if ($i + 2 > strlen($data)) break;
            
            $length = (int)substr($data, $i, 2);
            $i += 2;
            
            if ($i + $length > strlen($data)) break;
            
            $value = substr($data, $i, $length);
            $i += $length;
            
            $result[$tag] = $value;
        }
        
        return $result;
    }

    /**
     * Generate Deep Link for Bakong
     */
    public function generateDeepLink(string $qrCode, array $options = []): array
    {
        try {
            if (empty($this->token)) {
                return ['error' => 'Bakong token not configured'];
            }

            $payload = [
                'qr' => $qrCode,
            ];

            if (!empty($options['app_name'])) {
                $payload['appName'] = $options['app_name'];
            }
            if (!empty($options['app_icon_url'])) {
                $payload['appIconUrl'] = $options['app_icon_url'];
            }
            if (!empty($options['callback_url'])) {
                $payload['callbackUrl'] = $options['callback_url'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/v1/create_deeplink', $payload);

            return $response->json() ?? ['error' => 'No response'];
        } catch (\Exception $e) {
            Log::error('KHQR generateDeepLink error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}

