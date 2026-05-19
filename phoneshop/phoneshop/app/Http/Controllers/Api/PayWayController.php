<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Sale;
use App\Services\BakongService;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayWayController extends Controller
{
    private $payWayService;
    private $bakongService;

    public function __construct(PayWayService $payWayService, BakongService $bakongService)
    {
        $this->payWayService = $payWayService;
        $this->bakongService = $bakongService;
    }

    /**
     * Get PayWay merchant info
     */
    public function getMerchantInfo()
    {
        try {
            $merchantInfo = $this->payWayService->getMerchantInfo();

            return response()->json([
                'success' => true,
                'data' => $merchantInfo,
                'message' => 'PayWay merchant info retrieved',
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
     * Get supported currencies
     */
    public function getSupportedCurrencies()
    {
        try {
            $currencies = $this->payWayService->getSupportedCurrencies();

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
     * Create PayWay transaction
     */
    public function createTransaction(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string|unique:sales,bill_no',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
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
                'first_name' => $request->first_name ?? $user->name,
                'last_name' => $request->last_name ?? 'Customer',
                'phone' => $request->phone ?? $user->phone ?? '',
                'email' => $request->email ?? $user->email ?? '',
            ];

            $transaction = $this->payWayService->createTransaction(
                $request->amount,
                $request->bill_number,
                $request->description,
                $customerInfo
            );

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => 'PayWay transaction created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('PayWay transaction creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetail(Request $request)
    {
        $request->validate([
            'tran_id' => 'required|string',
        ]);

        try {
            $details = $this->payWayService->getTransactionDetail($request->tran_id);

            return response()->json([
                'success' => true,
                'data' => $details,
                'message' => 'Transaction details retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get transaction detail', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify transaction
     */
    public function verifyTransaction(Request $request)
    {
        $request->validate([
            'tran_id' => 'required|string',
        ]);

        try {
            $verification = $this->payWayService->verifyTransaction($request->tran_id);

            if ($verification['verified']) {
                DB::transaction(function () use ($request, $verification) {
                    $payment = Payment::firstOrCreate(
                        ['transaction_id' => $request->tran_id],
                        [
                            'bill_no' => $request->tran_id,
                            'amount' => $verification['amount'],
                            'payment_method' => 'cambodia_bank_transfer',
                            'gateway' => 'payway',
                            'status' => 'completed',
                            'response_data' => json_encode($verification),
                        ]
                    );

                    // Get sales and send notification
                    $sales = Sale::where('bill_no', $request->tran_id)->with('user', 'phone')->get();
                    if (!$sales->isEmpty()) {
                        $this->sendPaymentNotification($sales, $request->tran_id, $verification['amount']);
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
     * Handle PayWay webhook callback
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('PayWay webhook received', $request->all());

            if (!$this->payWayService->verifyWebhookCallback($request->all())) {
                Log::warning('PayWay webhook signature verification failed');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook signature',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $tranId = $request->input('tran_id');
            $status = $request->input('status');
            $amount = $request->input('amount');

            if ($status === 'success' || $status === 'AUTHORIZED') {
                DB::transaction(function () use ($tranId, $amount) {
                    $payment = Payment::firstOrCreate(
                        ['transaction_id' => $tranId],
                        [
                            'bill_no' => $tranId,
                            'amount' => $amount,
                            'payment_method' => 'cambodia_bank_transfer',
                            'gateway' => 'payway',
                            'status' => 'completed',
                            'response_data' => json_encode(request()->all()),
                        ]
                    );

                    // Get sales and send notification
                    $sales = Sale::where('bill_no', $tranId)->with('user', 'phone')->get();
                    if (!$sales->isEmpty()) {
                        $this->sendPaymentNotification($sales, $tranId, $amount);
                    }

                    Log::info('PayWay payment processed', [
                        'tran_id' => $tranId,
                        'amount' => $amount,
                        'status' => $status,
                    ]);
                });
            }

            return response()->json([
                'success' => true,
                'tran_id' => $tranId,
                'status' => $status,
                'message' => 'Webhook processed',
            ]);
        } catch (\Exception $e) {
            Log::error('PayWay webhook processing failed', ['error' => $e->getMessage()]);
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
            'tran_id' => 'required|string',
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        try {
            $refundResponse = $this->payWayService->refundTransaction(
                $request->tran_id,
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
    private function sendPaymentNotification($sales, $tranId, $amount)
    {
        try {
            $customer = $sales->first()->user;
            $itemsSummary = '';

            foreach ($sales as $sale) {
                $itemsSummary .= "• {$sale->phone->name} x{$sale->qty} - \${$sale->total_price}\n";
            }

            $message = "<b>🏦 PayWay Payment Confirmed!</b>\n\n";
            $message .= "<b>Transaction ID:</b> {$tranId}\n";
            if ($customer) {
                $message .= "<b>Customer:</b> {$customer->name}\n";
                $message .= "<b>Email:</b> {$customer->email}\n";
            }
            $message .= '<b>Amount:</b> $'.number_format($amount, 2)."\n";
            $message .= "<b>Items:</b>\n{$itemsSummary}";
            $message .= "<b>Status:</b> ✅ Payment Completed\n";
            $message .= "<b>Time:</b> ".now()->format('Y-m-d H:i:s')."\n";

            $this->bakongService->sendTelegramNotification($message);

            Log::info('PayWay payment notification sent', [
                'tran_id' => $tranId,
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification', ['error' => $e->getMessage()]);
        }
    }
}
