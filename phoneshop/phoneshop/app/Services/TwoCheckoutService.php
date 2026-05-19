<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwoCheckoutService
{
    private $merchantCode;
    private $secretKey;
    private $publishableKey;
    private $testMode;
    private $apiBaseUrl;

    public function __construct()
    {
        $this->merchantCode = env('TWOCHECKOUT_MERCHANT_CODE');
        $this->secretKey = env('TWOCHECKOUT_SECRET_KEY');
        $this->publishableKey = env('TWOCHECKOUT_PUBLISHABLE_KEY');
        $this->testMode = env('TWOCHECKOUT_TEST_MODE', true);
        $this->apiBaseUrl = $this->testMode 
            ? 'https://sandbox.2checkout.com/api'
            : 'https://api.2checkout.com/api';
    }

    /**
     * Create a payment session for Visa card
     */
    public function createPaymentSession($amount, $currency, $billNumber, $customerEmail, $customerName)
    {
        try {
            $payload = [
                'merchant' => $this->merchantCode,
                'amount' => intval($amount * 100), // Convert to cents
                'currency' => $currency,
                'orderref' => $billNumber,
                'email' => $customerEmail,
                'name' => $customerName,
                'test' => $this->testMode ? '1' : '0',
                'return_url' => route('shop.payment.callback'),
                'return_type' => 'json',
            ];

            $signature = $this->generateSignature($payload);
            $payload['signature'] = $signature;

            return $payload;
        } catch (\Exception $e) {
            Log::error('2Checkout payment session creation failed', [
                'error' => $e->getMessage(),
                'bill_number' => $billNumber,
            ]);
            throw new \Exception('Failed to create payment session: '.$e->getMessage());
        }
    }

    /**
     * Verify payment callback/webhook
     */
    public function verifyPaymentCallback($data)
    {
        try {
            $providedSignature = $data['signature'] ?? '';
            $callbackData = array_filter($data, fn ($key) => $key !== 'signature', ARRAY_FILTER_USE_KEY);

            $expectedSignature = $this->generateSignature($callbackData);

            if (! hash_equals($providedSignature, $expectedSignature)) {
                Log::warning('2Checkout signature verification failed', ['data' => $callbackData]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('2Checkout callback verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Process refund
     */
    public function refundPayment($transactionId, $amount, $reason = 'Customer Request')
    {
        try {
            $payload = [
                'merchant' => $this->merchantCode,
                'transaction_id' => $transactionId,
                'amount' => intval($amount * 100), // Convert to cents
                'reason' => $reason,
            ];

            $auth = base64_encode($this->merchantCode.':'.$this->secretKey);

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$auth,
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl.'/refund', $payload);

            if ($response->successful()) {
                Log::info('2Checkout refund processed successfully', [
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                ]);
                return $response->json();
            }

            Log::error('2Checkout refund failed', [
                'response' => $response->json(),
                'transaction_id' => $transactionId,
            ]);
            throw new \Exception('Refund failed: '.$response->body());
        } catch (\Exception $e) {
            Log::error('2Checkout refund processing error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetails($transactionId)
    {
        try {
            $auth = base64_encode($this->merchantCode.':'.$this->secretKey);

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$auth,
            ])->get($this->apiBaseUrl.'/transaction/'.$transactionId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch transaction details');
        } catch (\Exception $e) {
            Log::error('2Checkout get transaction error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate HMAC signature for payment request
     */
    private function generateSignature($data)
    {
        ksort($data);
        $signatureString = '';
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $signatureString .= $this->arrayToString($value);
            } else {
                $signatureString .= $value;
            }
        }

        return hash_hmac('md5', $signatureString, $this->secretKey);
    }

    /**
     * Convert array to string for signature generation
     */
    private function arrayToString($array)
    {
        $str = '';
        foreach ($array as $value) {
            if (is_array($value)) {
                $str .= $this->arrayToString($value);
            } else {
                $str .= $value;
            }
        }
        return $str;
    }

    /**
     * Transfer funds to merchant bank account
     * This would typically be handled by 2Checkout automatically,
     * but this method is for manual transfers or settlement management
     */
    public function requestFundTransfer($amount, $bankAccount = null)
    {
        try {
            $bankAccount = $bankAccount ?? env('MERCHANT_BANK_ACCOUNT');
            
            if (! $bankAccount) {
                throw new \Exception('Bank account not configured');
            }

            $payload = [
                'merchant' => $this->merchantCode,
                'amount' => intval($amount * 100),
                'bank_account' => $bankAccount,
                'description' => 'Fund transfer request from PhoneShop',
            ];

            $auth = base64_encode($this->merchantCode.':'.$this->secretKey);

            $response = Http::withHeaders([
                'Authorization' => 'Basic '.$auth,
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl.'/settlement/transfer', $payload);

            if ($response->successful()) {
                Log::info('Fund transfer request submitted', [
                    'amount' => $amount,
                    'bank_account' => substr($bankAccount, -4),
                ]);
                return $response->json();
            }

            throw new \Exception('Fund transfer failed: '.$response->body());
        } catch (\Exception $e) {
            Log::error('Fund transfer error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
