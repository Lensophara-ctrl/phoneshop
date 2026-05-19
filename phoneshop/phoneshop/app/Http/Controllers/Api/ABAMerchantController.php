<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Sale;
use App\Services\ABAMerchantService;
use App\Services\BakongService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ABAMerchantController extends Controller
{
    private $abaService;
    private $bakongService;

    public function __construct(ABAMerchantService $abaService, BakongService $bakongService)
    {
        $this->abaService = $abaService;
        $this->bakongService = $bakongService;
    }

    /**
     * Test ABA QR Code generation
     */
    public function testQRCode()
    {
        try {
            $transactionId = 'TEST-' . date('YmdHis');
            $amount = 0.01;
            
            $qrCode = $this->abaService->generatePaymentQR($transactionId, $amount);
            
            return response()->json([
                'success' => true,
                'data' => $qrCode,
                'test_info' => [
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'merchant_id' => $this->abaService->getMerchantInfo()['merchant_id'],
                    'timestamp' => now()->toIso8601String(),
                ],
                'message' => 'ABA QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('ABA test QR code failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get ABA Merchant info
     */
    public function getMerchantInfo()
    {
        try {
            $merchantInfo = $this->abaService->getMerchantInfo();

            return response()->json([
                'success' => true,
                'data' => $merchantInfo,
                'message' => 'ABA merchant info retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get merchant info', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Health check - verify ABA API connection
     */
    public function healthCheck()
    {
        try {
            $healthStatus = $this->abaService->healthCheck();

            return response()->json([
                'success' => $healthStatus['connected'],
                'data' => $healthStatus,
                'message' => $healthStatus['connected'] ? 'ABA API connected' : 'ABA API not responding',
            ]);
        } catch (\Exception $e) {
            Log::error('Health check failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies()
    {
        try {
            $currencies = $this->abaService->getSupportedCurrencies();

            return response()->json([
                'success' => true,
                'data' => $currencies,
                'message' => 'Supported currencies retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get currencies', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create ABA payment request
     */
    public function createPaymentRequest(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|string|unique:sales,bill_no',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
        ]);

        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $customerInfo = [
                'name' => $request->customer_name ?? $user->name,
                'email' => $request->customer_email ?? $user->email,
                'phone' => $request->customer_phone ?? '',
            ];

            $paymentRequest = $this->abaService->createPaymentRequest(
                $request->amount,
                $request->invoice_id,
                $request->description,
                $customerInfo
            );

            return response()->json([
                'success' => true,
                'data' => $paymentRequest,
                'message' => 'ABA payment request created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('ABA payment request creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        try {
            $status = $this->abaService->getPaymentStatus($request->transaction_id);

            return response()->json([
                'success' => true,
                'data' => $status,
                'message' => 'Payment status retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get payment status', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        try {
            $verification = $this->abaService->verifyPayment($request->transaction_id);

            if ($verification['verified']) {
                DB::transaction(function () use ($request, $verification) {
                    $payment = Payment::firstOrCreate(
                        ['transaction_id' => $request->transaction_id],
                        [
                            'bill_no' => $request->transaction_id,
                            'amount' => $verification['amount'],
                            'payment_method' => 'visa_card',
                            'gateway' => 'aba_merchant',
                            'status' => 'completed',
                            'response_data' => json_encode($verification),
                        ]
                    );

                    // Get sales and send notification
                    $sales = Sale::where('bill_no', $request->transaction_id)->with('user', 'phone')->get();
                    if (!$sales->isEmpty()) {
                        $this->sendPaymentNotification($sales, $request->transaction_id, $verification['amount']);
                    }
                });
            }

            return response()->json([
                'success' => $verification['verified'],
                'data' => $verification,
                'message' => $verification['verified'] ? 'Payment verified' : 'Payment not yet completed',
            ]);
        } catch (\Exception $e) {
            Log::error('Payment verification failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate payment QR code
     */
    public function generatePaymentQR(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $qrCode = $this->abaService->generatePaymentQR($request->transaction_id, $request->amount);

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($qrCode, [
                    'payment_method' => 'aba',
                    'instructions' => [
                        '1. Open ABA PAY app or website',
                        '2. Select "Scan QR Code" or "Enter Details"',
                        '3. Scan the QR code above',
                        '4. Review payment details',
                        '5. Enter PIN and confirm payment',
                        '6. Payment completed',
                    ],
                ]),
                'message' => 'ABA payment QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('ABA QR generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle ABA webhook callback
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('ABA webhook received', $request->all());

            if (!$this->abaService->verifyWebhookCallback($request->all())) {
                Log::warning('ABA webhook signature verification failed');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook signature',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $transactionId = $request->input('transaction_id');
            $status = $request->input('status');
            $amount = $request->input('amount');

            if ($status === 'completed' || $status === 'success' || $status === 'approved') {
                DB::transaction(function () use ($transactionId, $amount) {
                    $payment = Payment::firstOrCreate(
                        ['transaction_id' => $transactionId],
                        [
                            'bill_no' => $transactionId,
                            'amount' => $amount,
                            'payment_method' => 'visa_card',
                            'gateway' => 'aba_merchant',
                            'status' => 'completed',
                            'response_data' => json_encode(request()->all()),
                        ]
                    );

                    // Get sales and send notification
                    $sales = Sale::where('bill_no', $transactionId)->with('user', 'phone')->get();
                    if (!$sales->isEmpty()) {
                        $this->sendPaymentNotification($sales, $transactionId, $amount);
                    }

                    Log::info('ABA payment processed', [
                        'transaction_id' => $transactionId,
                        'amount' => $amount,
                        'status' => $status,
                    ]);
                });
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => $status,
                'message' => 'Webhook processed',
            ]);
        } catch (\Exception $e) {
            Log::error('ABA webhook processing failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Request refund
     */
    public function refund(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        try {
            $refundResponse = $this->abaService->refundPayment(
                $request->transaction_id,
                $request->amount,
                $request->reason ?? 'Refund request'
            );

            return response()->json([
                'success' => true,
                'data' => $refundResponse,
                'message' => 'Refund request submitted',
            ]);
        } catch (\Exception $e) {
            Log::error('Refund request failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send payment notification to Telegram
     */
    private function sendPaymentNotification($sales, $transactionId, $amount)
    {
        try {
            $customer = $sales->first()->user;
            $itemsSummary = '';

            foreach ($sales as $sale) {
                $itemsSummary .= "• {$sale->phone->name} x{$sale->qty} - \${$sale->total_price}\n";
            }

            $message = "<b>💳 ABA Payment Confirmed!</b>\n\n";
            $message .= "<b>Transaction ID:</b> {$transactionId}\n";
            if ($customer) {
                $message .= "<b>Customer:</b> {$customer->name}\n";
                $message .= "<b>Email:</b> {$customer->email}\n";
            }
            $message .= '<b>Amount:</b> $'.number_format($amount, 2)."\n";
            $message .= "<b>Items:</b>\n{$itemsSummary}";
            $message .= "<b>Status:</b> ✅ Payment Completed\n";
            $message .= "<b>Time:</b> ".now()->format('Y-m-d H:i:s')."\n";

            $this->bakongService->sendTelegramNotification($message);

            Log::info('ABA payment notification sent', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', ['error' => $e->getMessage()]);
        }
    }
}
