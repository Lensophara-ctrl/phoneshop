<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayWayService
{
    private $merchantId;
    private $apiKey;
    private $apiSecret;
    private $sandboxMode;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = env('PAYWAY_MERCHANT_ID');
        $this->apiKey = env('PAYWAY_API_KEY');
        $this->apiSecret = env('PAYWAY_API_SECRET');
        $this->sandboxMode = env('PAYWAY_SANDBOX_MODE', true);
        $this->baseUrl = $this->sandboxMode
            ? env('PAYWAY_SANDBOX_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1')
            : env('PAYWAY_PRODUCTION_URL', 'https://checkout.payway.com.kh/api/payment-gateway/v1');
    }

    /**
     * Create a payment transaction
     */
    public function createTransaction($amount, $billNumber, $description, $customerInfo = [])
    {
        try {
            $reqTime = $this->generateRequestTime();
            
            $payload = [
                'req_time' => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id' => $billNumber,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'USD',
                'fname' => $customerInfo['first_name'] ?? 'Customer',
                'lname' => $customerInfo['last_name'] ?? 'Payment',
                'phone' => $customerInfo['phone'] ?? '',
                'email' => $customerInfo['email'] ?? '',
                'items' => $description,
                'return_url' => route('shop.payment.callback'),
                'cancel_url' => route('shop.home'),
                'hash' => '',
            ];

            // Generate hash
            $payload['hash'] = $this->generateHash($payload);

            Log::info('PayWay transaction created', [
                'tran_id' => $billNumber,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'tran_id' => $billNumber,
                'amount' => $amount,
                'currency' => 'USD',
                'request_data' => $payload,
                'payment_url' => $this->baseUrl.'/payments/create',
            ];
        } catch (\Exception $e) {
            Log::error('PayWay transaction creation failed', [
                'error' => $e->getMessage(),
                'bill_number' => $billNumber,
            ]);
            throw $e;
        }
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetail($tranId)
    {
        try {
            $reqTime = $this->generateRequestTime();
            
            $payload = [
                'req_time' => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id' => $tranId,
                'hash' => '',
            ];

            // Generate hash
            $payload['hash'] = $this->generateHash($payload);

            $response = Http::post(
                $this->baseUrl.'/payments/transaction-detail',
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('PayWay transaction details retrieved', [
                    'tran_id' => $tranId,
                    'status' => $data['status'] ?? 'unknown',
                ]);

                return $data;
            }

            throw new \Exception('Failed to get transaction details: '.$response->body());
        } catch (\Exception $e) {
            Log::error('PayWay get transaction detail failed', [
                'error' => $e->getMessage(),
                'tran_id' => $tranId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify transaction status
     */
    public function verifyTransaction($tranId)
    {
        try {
            $transactionDetail = $this->getTransactionDetail($tranId);

            $isSuccess = isset($transactionDetail['status']) && 
                         ($transactionDetail['status'] === 'success' || 
                          $transactionDetail['status'] === 'AUTHORIZED');

            return [
                'verified' => $isSuccess,
                'status' => $transactionDetail['status'] ?? 'unknown',
                'tran_id' => $tranId,
                'amount' => $transactionDetail['amount'] ?? 0,
                'data' => $transactionDetail,
            ];
        } catch (\Exception $e) {
            Log::error('PayWay transaction verification failed', [
                'error' => $e->getMessage(),
                'tran_id' => $tranId,
            ]);
            throw $e;
        }
    }

    /**
     * Process refund
     */
    public function refundTransaction($tranId, $amount = null, $reason = 'Customer Request')
    {
        try {
            $reqTime = $this->generateRequestTime();
            
            $payload = [
                'req_time' => $reqTime,
                'merchant_id' => $this->merchantId,
                'tran_id' => $tranId,
                'refund_amount' => $amount ? number_format($amount, 2, '.', '') : '',
                'reason' => $reason,
                'hash' => '',
            ];

            // Generate hash
            $payload['hash'] = $this->generateHash($payload);

            $response = Http::post(
                $this->baseUrl.'/payments/refund',
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('PayWay refund processed', [
                    'tran_id' => $tranId,
                    'amount' => $amount,
                    'response' => $data,
                ]);

                return $data;
            }

            throw new \Exception('Refund failed: '.$response->body());
        } catch (\Exception $e) {
            Log::error('PayWay refund failed', [
                'error' => $e->getMessage(),
                'tran_id' => $tranId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook callback
     */
    public function verifyWebhookCallback($data)
    {
        try {
            $providedHash = $data['hash'] ?? '';
            $callbackData = array_filter($data, fn ($key) => $key !== 'hash', ARRAY_FILTER_USE_KEY);

            $expectedHash = $this->generateHash($callbackData);

            if (!hash_equals($providedHash, $expectedHash)) {
                Log::warning('PayWay webhook signature verification failed', ['data' => $callbackData]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('PayWay webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate hash for API requests
     */
    private function generateHash($data)
    {
        // Remove hash from data if it exists
        unset($data['hash']);

        // Sort data by keys
        ksort($data);

        // Create string from sorted data
        $hashString = '';
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $hashString .= $value;
            }
        }

        // Append API secret
        $hashString .= $this->apiSecret;

        // Generate MD5 hash
        return strtoupper(md5($hashString));
    }

    /**
     * Generate request time in format YYYYMMDDHHmmss
     */
    private function generateRequestTime()
    {
        return now()->format('YmdHis');
    }

    /**
     * Get list of supported currencies
     */
    public function getSupportedCurrencies()
    {
        return [
            'USD' => 'US Dollar',
            'KHR' => 'Cambodian Riel',
        ];
    }

    /**
     * Get merchant info
     */
    public function getMerchantInfo()
    {
        return [
            'merchant_id' => $this->merchantId,
            'sandbox_mode' => $this->sandboxMode,
            'api_url' => $this->baseUrl,
        ];
    }
}
