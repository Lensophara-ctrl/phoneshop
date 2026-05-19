<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\QRCodeService;

class ABAMerchantService
{
    private $merchantId;
    private $merchantName;
    private $apiUsername;
    private $apiPassword;
    private $apiKey;
    private $storeId;
    private $sandboxMode;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = env('ABA_MERCHANT_ID');
        $this->merchantName = env('ABA_MERCHANT_NAME');
        $this->apiUsername = env('ABA_API_USERNAME');
        $this->apiPassword = env('ABA_API_PASSWORD');
        $this->apiKey = env('ABA_API_KEY');
        $this->storeId = env('ABA_STORE_ID');
        $this->sandboxMode = env('ABA_SANDBOX_MODE', true);
        $this->baseUrl = $this->sandboxMode
            ? env('ABA_SANDBOX_URL', 'https://checkout-sandbox.ababank.com/api')
            : env('ABA_PRODUCTION_URL', 'https://checkout.ababank.com/api');
    }

    /**
     * Create a payment request
     */
    public function createPaymentRequest($amount, $invoiceId, $description, $customerInfo = [])
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'merchant_name' => $this->merchantName,
                'store_id' => $this->storeId,
                'transaction_id' => $invoiceId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'USD',
                'description' => $description,
                'return_url' => route('shop.payment.callback'),
                'cancel_url' => route('shop.home'),
                'customer_email' => $customerInfo['email'] ?? '',
                'customer_phone' => $customerInfo['phone'] ?? '',
                'customer_name' => $customerInfo['name'] ?? '',
            ];

            // Generate request signature
            $payload['signature'] = $this->generateSignature($payload);

            Log::info('ABA payment request created', [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $invoiceId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'transaction_id' => $invoiceId,
                'amount' => $amount,
                'currency' => 'USD',
                'request_data' => $payload,
                'payment_url' => $this->baseUrl.'/checkout',
                'qr_endpoint' => $this->baseUrl.'/qr',
            ];
        } catch (\Exception $e) {
            Log::error('ABA payment request creation failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoiceId,
            ]);
            throw $e;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($transactionId)
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'timestamp' => now()->timestamp,
            ];

            $payload['signature'] = $this->generateSignature($payload);

            $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                ->post($this->baseUrl.'/payment-status', $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('ABA payment status retrieved', [
                    'transaction_id' => $transactionId,
                    'status' => $data['status'] ?? 'unknown',
                ]);

                return $data;
            }

            throw new \Exception('Failed to get payment status: '.$response->body());
        } catch (\Exception $e) {
            Log::error('ABA get payment status failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment($transactionId)
    {
        try {
            $paymentStatus = $this->getPaymentStatus($transactionId);

            $isSuccess = isset($paymentStatus['status']) && 
                         ($paymentStatus['status'] === 'completed' || 
                          $paymentStatus['status'] === 'success' ||
                          $paymentStatus['status'] === 'approved');

            return [
                'verified' => $isSuccess,
                'status' => $paymentStatus['status'] ?? 'unknown',
                'transaction_id' => $transactionId,
                'amount' => $paymentStatus['amount'] ?? 0,
                'data' => $paymentStatus,
            ];
        } catch (\Exception $e) {
            Log::error('ABA payment verification failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);
            throw $e;
        }
    }

    /**
     * Process refund
     */
    public function refundPayment($transactionId, $amount = null, $reason = 'Customer Request')
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'transaction_id' => $transactionId,
                'refund_amount' => $amount ? number_format($amount, 2, '.', '') : 'full',
                'reason' => $reason,
                'timestamp' => now()->timestamp,
            ];

            $payload['signature'] = $this->generateSignature($payload);

            $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                ->post($this->baseUrl.'/refund', $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('ABA refund processed', [
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'response' => $data,
                ]);

                return $data;
            }

            throw new \Exception('Refund failed: '.$response->body());
        } catch (\Exception $e) {
            Log::error('ABA refund failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
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
            $providedSignature = $data['signature'] ?? '';
            $callbackData = array_filter($data, fn ($key) => $key !== 'signature', ARRAY_FILTER_USE_KEY);

            $expectedSignature = $this->generateSignature($callbackData);

            if (!hash_equals($providedSignature, $expectedSignature)) {
                Log::warning('ABA webhook signature verification failed', ['data' => $callbackData]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('ABA webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate payment QR code
     */
    public function generatePaymentQR($transactionId, $amount)
    {
        try {
            $bakongService = new BakongService();
            $khqrResult = $bakongService->generateKHQR($amount, $transactionId, 'USD');
            
            // Extract QR string from result (now returns array)
            $khqr = is_array($khqrResult) && isset($khqrResult['data']['qr']) 
                ? $khqrResult['data']['qr'] 
                : $khqrResult;

            return [
                'data' => $khqr,
                'image_uri' => QRCodeService::generateQRCodeDataURI($khqr),
                'payment_method' => 'aba_pay',
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ];
        } catch (\Exception $e) {
            Log::error('ABA QR code generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate signature for API requests
     */
    private function generateSignature($data)
    {
        try {
            // Remove signature from data if it exists
            unset($data['signature']);

            // Sort data by keys
            ksort($data);

            // Create string from sorted data
            $signatureString = '';
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    $signatureString .= $value;
                }
            }

            // Append API key
            $signatureString .= $this->apiKey;

            // Generate SHA256 hash
            return hash('sha256', $signatureString);
        } catch (\Exception $e) {
            Log::error('Signature generation error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get merchant info
     */
    public function getMerchantInfo()
    {
        return [
            'merchant_id' => $this->merchantId,
            'merchant_name' => $this->merchantName,
            'store_id' => $this->storeId,
            'sandbox_mode' => $this->sandboxMode,
            'api_url' => $this->baseUrl,
        ];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies()
    {
        return [
            'USD' => 'US Dollar',
            'KHR' => 'Cambodian Riel',
        ];
    }

    /**
     * Health check - verify API connection
     */
    public function healthCheck()
    {
        try {
            $payload = [
                'merchant_id' => $this->merchantId,
                'timestamp' => now()->timestamp,
            ];

            $payload['signature'] = $this->generateSignature($payload);

            $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                ->timeout(5)
                ->post($this->baseUrl.'/health-check', $payload);

            Log::info('ABA health check', ['status' => $response->status()]);

            return [
                'status' => $response->status(),
                'connected' => $response->successful(),
            ];
        } catch (\Exception $e) {
            Log::warning('ABA health check failed', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
