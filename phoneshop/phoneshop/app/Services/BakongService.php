<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BakongService
{
    protected string $apiUrl;
    protected string $token;
    protected array $merchant;

    public function __construct()
    {
        // Priority: .env > config
        $this->apiUrl = env('BAKONG_API_URL', config('services.bakong.api_url', 'https://api-bakong.nbc.gov.kh'));
        $this->token = env('BAKONG_TOKEN', config('services.bakong.token', ''));
        
        $this->merchant = [
            'bakong_id' => env('MERCHANT_BAKONG_ID', config('services.bakong.merchant.bakong_id', '')),
            'name' => env('MERCHANT_NAME', config('services.bakong.merchant.name', 'PhoneShop')),
            'city' => env('MERCHANT_CITY', config('services.bakong.merchant.city', 'Phnom Penh')),
        ];
    }

    /**
     * Generate KHQR for payment (alias for generateKHQR)
     */
    public function generateKHQR($amount, $billNumber = null, $currency = 'USD')
    {
        return $this->generateIndividualQR([
            'amount' => $amount,
            'bill_number' => $billNumber,
            'currency' => $currency,
        ]);
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
     * Build KHQR String according to Bakong KHQR specification
     * Based on official Bakong KHQR format
     */
    protected function buildKHQRString(array $data): string
    {
        $bakongId = $data['bakong_account_id'] ?? $this->merchant['bakong_id'] ?? env('MERCHANT_BAKONG_ID', '');
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'USD';
        $merchantName = $data['merchant_name'] ?? $this->merchant['name'] ?? env('MERCHANT_NAME', 'PhoneShop');
        $merchantCity = $data['merchant_city'] ?? $this->merchant['city'] ?? env('MERCHANT_CITY', 'Phnom Penh');
        $billNumber = $data['bill_number'] ?? '';
        $acquiringBank = $data['acquiring_bank'] ?? env('ACQUIRING_BANK', 'Bakong');

        // Validate Bakong ID
        if (empty($bakongId)) {
            throw new \Exception('Bakong ID is not configured. Please set MERCHANT_BAKONG_ID in .env file.');
        }

        // Ensure amount is at least 0.01 for USD
        if ($currency === 'USD' && $amount < 0.01) {
            $amount = 0.01;
        }

        // Build QR data according to Bakong KHQR specification
        $qr = '';
        
        // 00: Payload Format Indicator
        $qr .= $this->tlv('00', '01');
        
        // 01: Point of Initiation Method (11 = Static, 12 = Dynamic)
        $qr .= $this->tlv('01', '12');
        
        // 29: Merchant Account Information (Bakong specific)
        // Must include: 00=Account ID, 02=Acquiring Bank
        $merchantInfo = '';
        $merchantInfo .= $this->tlv('00', $bakongId); // Bakong Account ID
        $merchantInfo .= $this->tlv('02', $acquiringBank); // Acquiring Bank
        $qr .= $this->tlv('29', $merchantInfo);
        
        // 52: Merchant Category Code
        $qr .= $this->tlv('52', '5999');
        
        // 53: Transaction Currency (840 = USD, 116 = KHR)
        $currencyCode = $currency === 'KHR' ? '116' : '840';
        $qr .= $this->tlv('53', $currencyCode);
        
        // 54: Transaction Amount (required for dynamic QR)
        $formattedAmount = number_format($amount, 2, '.', '');
        $qr .= $this->tlv('54', $formattedAmount);
        
        // 58: Country Code
        $qr .= $this->tlv('58', 'KH');
        
        // 59: Merchant Name (max 25 chars)
        $qr .= $this->tlv('59', substr($merchantName, 0, 25));
        
        // 60: Merchant City (max 15 chars)
        $qr .= $this->tlv('60', substr($merchantCity, 0, 15));
        
        // 62: Additional Data Field Template
        if (!empty($billNumber)) {
            $additionalData = $this->tlv('01', substr($billNumber, 0, 25));
            $qr .= $this->tlv('62', $additionalData);
        }
        
        // 99: Timestamp Information (creation and expiration)
        $creationTimestamp = (string)(time() * 1000); // milliseconds
        $expirationTimestamp = (string)((time() + 900) * 1000); // 15 minutes expiry in milliseconds
        
        $timestampInfo = '';
        $timestampInfo .= $this->tlv('00', $creationTimestamp);
        $timestampInfo .= $this->tlv('01', $expirationTimestamp);
        $qr .= $this->tlv('99', $timestampInfo);
        
        // 63: CRC (must be last, calculated over everything before it)
        $qr .= '6304'; // Placeholder
        
        // Calculate and append CRC
        $crc = $this->crc16($qr);
        $qr = substr($qr, 0, -4) . '6304' . strtoupper(sprintf('%04X', $crc));
        
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
                Log::error('Bakong token not configured');
                return [
                    'responseCode' => -1,
                    'responseMessage' => 'Bakong token not configured',
                    'data' => ['status' => 'ERROR']
                ];
            }

            Log::info('Checking Bakong payment with MD5: ' . $md5);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($this->apiUrl . '/v1/check_transaction_by_md5', [
                'md5' => $md5,
            ]);

            $result = $response->json();
            
            Log::info('Bakong API response:', $result ?? ['raw' => $response->body()]);

            if (!$result) {
                return [
                    'responseCode' => -1,
                    'responseMessage' => 'No response from Bakong API',
                    'data' => ['status' => 'ERROR']
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Bakong checkPayment error: ' . $e->getMessage());
            return [
                'responseCode' => -1,
                'responseMessage' => $e->getMessage(),
                'data' => ['status' => 'ERROR']
            ];
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
            Log::error('Bakong verifyQR error: ' . $e->getMessage());
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
            Log::error('Bakong decodeQR error: ' . $e->getMessage());
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
                case '00': // Payload Format Indicator
                    $result['format_indicator'] = $value;
                    break;
                case '01': // Point of Initiation Method
                    $result['initiation_method'] = $value;
                    break;
                case '29': // Merchant Account Information
                    $result['merchant_account'] = $this->parseNestedTLV($value);
                    break;
                case '52': // Merchant Category Code
                    $result['merchant_category_code'] = $value;
                    break;
                case '53': // Transaction Currency
                    $result['currency'] = $value;
                    break;
                case '54': // Transaction Amount
                    $result['amount'] = $value;
                    break;
                case '58': // Country Code
                    $result['country_code'] = $value;
                    break;
                case '59': // Merchant Name
                    $result['merchant_name'] = $value;
                    break;
                case '60': // Merchant City
                    $result['merchant_city'] = $value;
                    break;
                case '62': // Additional Data
                    $result['additional_data'] = $this->parseNestedTLV($value);
                    break;
                case '63': // CRC
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
            Log::error('Bakong generateDeepLink error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    private function serializeKHQR($data)
    {
        $result = '';
        foreach ($data as $id => $value) {
            if ($id === '63') {
                continue;
            }

            if (is_array($value)) {
                $value = $this->serializeKHQR($value);
            }

            $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
            $result .= $id.$length.$value;
        }

        return $result;
    }

    /**
     * Legacy method for backward compatibility
     */
    private function calculateCRC($data)
    {
        $crc = 0xFFFF;
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc <<= 1;
                }
            }
        }
        $crc &= 0xFFFF;

        return str_pad(dechex($crc), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Legacy method for backward compatibility
     */
    public function sendTelegramNotification($message)
    {
        $telegramService = app(\App\Services\TelegramService::class);
        return $telegramService->sendMessage($message);
    }

    // =====================================================
    // BAKONG CHECKOUT (Dynamic Payment) METHODS
    // =====================================================

    /**
     * Check if Bakong Checkout is enabled
     */
    public function isCheckoutEnabled(): bool
    {
        return config('services.bakong.checkout.enabled', false) === true 
            && !empty(config('services.bakong.checkout.merchant_id'))
            && !empty($this->token);
    }

    /**
     * Create Bakong Checkout Session
     * This creates a dynamic payment link that redirects to Bakong
     */
    public function createCheckoutSession(array $data): array
    {
        try {
            if (!$this->isCheckoutEnabled()) {
                return [
                    'success' => false,
                    'error' => 'Bakong Checkout is not configured'
                ];
            }

            $checkoutConfig = config('services.bakong.checkout');
            
            $payload = [
                'merchantId' => $checkoutConfig['merchant_id'],
                'amount' => number_format($data['amount'], 2, '.', ''),
                'currency' => $data['currency'] ?? 'USD',
                'referenceId' => $data['bill_number'] ?? uniqid('INV-'),
                'description' => $data['description'] ?? 'Payment for Order ' . ($data['bill_number'] ?? ''),
                'callbackUrl' => $checkoutConfig['callback_url'] ?? route('webhook.bakong.payment'),
                'returnUrl' => $checkoutConfig['return_url'] ?? route('shop.success', ['billNo' => $data['bill_number']]),
                'expiresAt' => now()->addHours(2)->toIso8601String(),
            ];

            Log::info('Bakong Checkout request:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($checkoutConfig['api_url'] . '/v1/checkout/create', $payload);

            $result = $response->json();
            
            Log::info('Bakong Checkout response:', $result ?? ['raw' => $response->body()]);

            if (isset($result['responseCode']) && $result['responseCode'] === 0) {
                return [
                    'success' => true,
                    'checkout_url' => $result['data']['paymentUrl'] ?? $result['paymentUrl'] ?? null,
                    'token' => $result['data']['token'] ?? $result['token'] ?? null,
                    'reference_id' => $payload['referenceId'],
                ];
            }

            return [
                'success' => false,
                'error' => $result['responseMessage'] ?? 'Failed to create checkout session',
                'details' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('Bakong Checkout error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check Bakong Checkout Payment Status
     */
    public function checkCheckoutStatus(string $checkoutToken): array
    {
        try {
            if (!$this->isCheckoutEnabled()) {
                return [
                    'success' => false,
                    'error' => 'Bakong Checkout is not configured'
                ];
            }

            $checkoutConfig = config('services.bakong.checkout');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($checkoutConfig['api_url'] . '/v1/checkout/status', [
                'token' => $checkoutToken,
            ]);

            $result = $response->json();

            if (isset($result['responseCode']) && $result['responseCode'] === 0) {
                return [
                    'success' => true,
                    'paid' => ($result['data']['status'] ?? $result['status'] ?? '') === 'COMPLETED',
                    'status' => $result['data']['status'] ?? $result['status'] ?? 'UNKNOWN',
                    'amount' => $result['data']['amount'] ?? $result['amount'] ?? 0,
                    'transaction_id' => $result['data']['transactionId'] ?? $result['transactionId'] ?? null,
                ];
            }

            return [
                'success' => false,
                'status' => $result['data']['status'] ?? 'UNKNOWN',
                'error' => $result['responseMessage'] ?? 'Failed to check status',
            ];

        } catch (\Exception $e) {
            Log::error('Bakong Checkout status error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
