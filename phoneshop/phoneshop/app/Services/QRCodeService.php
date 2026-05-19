<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class QRCodeService
{
    /**
     * Generate QR code as Data URI (PNG image)
     * Uses Google Charts API with fallback
     */
    public static function generateQRCodeDataURI($data, $size = 300)
    {
        try {
            // Encode data for URL
            $encodedData = urlencode($data);
            
            // Google Charts QR Code API
            $qrUrl = "https://chart.googleapis.com/chart?chs={$size}x{$size}&chd=D_&cht=qr&chl={$encodedData}";
            
            // Try to get QR code image using file_get_contents
            $imageData = @file_get_contents($qrUrl);
            
            if ($imageData === false) {
                Log::warning('file_get_contents failed for QR code, trying cURL');
                
                // Fallback to cURL if file_get_contents fails
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $qrUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 5,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]);
                    
                    $imageData = curl_exec($ch);
                    $error = curl_error($ch);
                    curl_close($ch);
                    
                    if ($imageData === false) {
                        Log::warning('cURL failed for QR code', ['error' => $error]);
                        return null;
                    }
                } else {
                    Log::warning('Cannot fetch QR code: file_get_contents and cURL both unavailable');
                    return null;
                }
            }
            
            // Convert to base64 data URI
            $base64 = base64_encode($imageData);
            return 'data:image/png;base64,'.$base64;
        } catch (\Exception $e) {
            Log::error('QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate QR code URL (for external use)
     */
    public static function generateQRCodeURL($data, $size = 300)
    {
        try {
            $encodedData = urlencode($data);
            return "https://chart.googleapis.com/chart?chs={$size}x{$size}&chd=D_&cht=qr&chl={$encodedData}";
        } catch (\Exception $e) {
            Log::error('QR code URL generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate PayWay Payment QR Code
     */
    public static function generatePayWayQRCode($tranId, $amount)
    {
        try {
            // PayWay QR format: merchant_id|tran_id|amount
            $qrData = "PAYWAY|{$tranId}|{$amount}|USD";
            
            return [
                'data' => $qrData,
                'image_uri' => self::generateQRCodeDataURI($qrData),
                'image_url' => self::generateQRCodeURL($qrData),
            ];
        } catch (\Exception $e) {
            Log::error('PayWay QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate Bank Transfer QR Code (KHQR format)
     */
    public static function generateBankTransferQRCode($khqr)
    {
        try {
            return [
                'data' => $khqr,
                'image_uri' => self::generateQRCodeDataURI($khqr),
                'image_url' => self::generateQRCodeURL($khqr),
                'type' => 'KHQR',
            ];
        } catch (\Exception $e) {
            Log::error('Bank transfer QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate Payment Instructions with QR Code
     */
    public static function generatePaymentQRWithInstructions($tranId, $amount, $paymentMethod = 'payway')
    {
        try {
            $qrCode = null;
            $instructions = [];

            switch ($paymentMethod) {
                case 'payway':
                    $qrCode = self::generatePayWayQRCode($tranId, $amount);
                    $instructions = [
                        '1. Open PayWay app or website',
                        '2. Scan the QR code above',
                        '3. Confirm payment details',
                        '4. Complete the transaction',
                    ];
                    break;

                case 'bank_transfer':
                case 'khqr':
                    $instructions = [
                        '1. Open your bank app',
                        '2. Select "Scan KHQR" or "Transfer by QR"',
                        '3. Scan the QR code above',
                        '4. Review and confirm payment',
                        '5. Complete the transaction',
                    ];
                    break;

                case '2checkout':
                    $instructions = [
                        '1. Click payment link or scan QR',
                        '2. Enter card details',
                        '3. Complete 3D Secure verification',
                        '4. Payment confirmed',
                    ];
                    break;
            }

            return [
                'success' => true,
                'payment_method' => $paymentMethod,
                'transaction_id' => $tranId,
                'amount' => $amount,
                'qr_code' => $qrCode,
                'instructions' => $instructions,
            ];
        } catch (\Exception $e) {
            Log::error('Payment QR with instructions error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate Invoice QR Code (with bill number and amount)
     */
    public static function generateInvoiceQRCode($billNumber, $amount, $customerEmail = null)
    {
        try {
            // Invoice QR format: INVOICE|billno|amount|email
            $qrData = "INVOICE|{$billNumber}|{$amount}";
            if ($customerEmail) {
                $qrData .= "|{$customerEmail}";
            }

            return [
                'bill_number' => $billNumber,
                'amount' => $amount,
                'qr_data' => $qrData,
                'image_uri' => self::generateQRCodeDataURI($qrData),
                'image_url' => self::generateQRCodeURL($qrData),
            ];
        } catch (\Exception $e) {
            Log::error('Invoice QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate ABA Payment QR Code
     */
    public static function generateABAQRCode($transactionId, $amount, $merchantId = null)
    {
        try {
            $qrData = "ABA|{$transactionId}|{$amount}|USD";
            if ($merchantId) {
                $qrData = "{$merchantId}|{$transactionId}|{$amount}|USD";
            }

            return [
                'data' => $qrData,
                'image_uri' => self::generateQRCodeDataURI($qrData),
                'image_url' => self::generateQRCodeURL($qrData),
                'payment_method' => 'aba',
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ];
        } catch (\Exception $e) {
            Log::error('ABA QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate Contact Card QR Code (vCard format)
     */
    public static function generateContactQRCode($name, $email, $phone = null, $website = null)
    {
        try {
            // vCard format
            $vcard = "BEGIN:VCARD\n";
            $vcard .= "VERSION:3.0\n";
            $vcard .= "FN:{$name}\n";
            if ($email) {
                $vcard .= "EMAIL:{$email}\n";
            }
            if ($phone) {
                $vcard .= "TEL:{$phone}\n";
            }
            if ($website) {
                $vcard .= "URL:{$website}\n";
            }
            $vcard .= "END:VCARD";

            return [
                'type' => 'vCard',
                'name' => $name,
                'qr_data' => $vcard,
                'image_uri' => self::generateQRCodeDataURI($vcard),
                'image_url' => self::generateQRCodeURL($vcard),
            ];
        } catch (\Exception $e) {
            Log::error('Contact QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
