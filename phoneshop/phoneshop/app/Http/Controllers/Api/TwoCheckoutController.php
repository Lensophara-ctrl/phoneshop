<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\Sale;
use App\Services\BakongService;
use App\Services\TwoCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwoCheckoutController extends Controller
{
    private $twoCheckoutService;
    private $bakongService;

    public function __construct(TwoCheckoutService $twoCheckoutService, BakongService $bakongService)
    {
        $this->twoCheckoutService = $twoCheckoutService;
        $this->bakongService = $bakongService;
    }

    /**
     * Initiate 2Checkout payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string|unique:sales,bill_no',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
        ]);

        try {
            $user = auth()->user() ?? $request->user();
            
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $paymentSession = $this->twoCheckoutService->createPaymentSession(
                $request->amount,
                $request->currency,
                $request->bill_number,
                $user->email,
                $user->name
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_url' => $this->getPaymentUrl(),
                    'merchant_code' => env('TWOCHECKOUT_MERCHANT_CODE'),
                    'publishable_key' => env('TWOCHECKOUT_PUBLISHABLE_KEY'),
                    'payment_data' => $paymentSession,
                ],
                'message' => 'Payment session created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('2Checkout payment initiation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle 2Checkout payment callback
     */
    public function callback(Request $request)
    {
        try {
            Log::info('2Checkout callback received', $request->all());

            if (! $this->twoCheckoutService->verifyPaymentCallback($request->all())) {
                Log::warning('2Checkout payment signature verification failed');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment signature',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $transactionId = $request->input('transaction_id');
            $billNumber = $request->input('orderref');
            $amount = $request->input('amount') / 100; // Convert from cents
            $status = $request->input('status');

            // Verify payment status
            if ($status !== 'approved' && $status !== 'complete') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not approved',
                    'status' => $status,
                ], Response::HTTP_BAD_REQUEST);
            }

            // Store payment transaction and send notification
            DB::transaction(function () use ($transactionId, $billNumber, $amount) {
                // Create or update payment record
                $payment = \App\Models\Payment::firstOrCreate(
                    ['transaction_id' => $transactionId],
                    [
                        'bill_no' => $billNumber,
                        'amount' => $amount,
                        'payment_method' => 'visa_card',
                        'gateway' => '2checkout',
                        'status' => 'completed',
                        'response_data' => json_encode(request()->all()),
                    ]
                );

                // Get sales and customer info
                $sales = Sale::where('bill_no', $billNumber)->with('user', 'phone')->get();
                
                if ($sales->isEmpty()) {
                    Log::warning('No sales found for bill number', ['bill_no' => $billNumber]);
                    return;
                }

                // Build notification message
                $customer = $sales->first()->user;
                $itemsSummary = '';
                
                foreach ($sales as $sale) {
                    $itemsSummary .= "• {$sale->phone->name} x{$sale->qty} - \${$sale->total_price}\n";
                }

                $message = "<b>💳 Visa Card Payment Confirmed (2Checkout)!</b>\n\n";
                $message .= "<b>Bill No:</b> #{$billNumber}\n";
                $message .= "<b>Transaction ID:</b> {$transactionId}\n";
                if ($customer) {
                    $message .= "<b>Customer:</b> {$customer->name}\n";
                    $message .= "<b>Email:</b> {$customer->email}\n";
                }
                $message .= '<b>Total:</b> $'.number_format($amount, 2)."\n";
                $message .= "<b>Items:</b>\n{$itemsSummary}";
                $message .= "<b>Status:</b> ✅ Payment Completed\n";
                $message .= "<b>Time:</b> ".now()->format('Y-m-d H:i:s')."\n";

                // Send notification
                $this->bakongService->sendTelegramNotification($message);

                Log::info('Payment notification sent', [
                    'bill_no' => $billNumber,
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                ]);
            });

            return response()->json([
                'success' => true,
                'bill_no' => $billNumber,
                'transaction_id' => $transactionId,
                'message' => 'Payment processed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('2Checkout callback processing error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        try {
            $transactionDetails = $this->twoCheckoutService->getTransactionDetails(
                $request->transaction_id
            );

            return response()->json([
                'success' => true,
                'data' => $transactionDetails,
                'message' => 'Payment status retrieved',
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
     * Request refund
     */
    public function refund(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        try {
            $refundResponse = $this->twoCheckoutService->refundPayment(
                $request->transaction_id,
                $request->amount,
                $request->reason ?? 'Refund request'
            );

            Log::info('Refund processed', [
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'data' => $refundResponse,
                'message' => 'Refund processed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Refund processing failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Transfer funds to merchant account
     */
    public function transferFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_account' => 'nullable|string',
        ]);

        try {
            $transferResponse = $this->twoCheckoutService->requestFundTransfer(
                $request->amount,
                $request->bank_account
            );

            Log::info('Fund transfer requested', [
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => true,
                'data' => $transferResponse,
                'message' => 'Fund transfer request submitted',
            ]);
        } catch (\Exception $e) {
            Log::error('Fund transfer failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get payment URL based on environment
     */
    private function getPaymentUrl()
    {
        return env('TWOCHECKOUT_TEST_MODE')
            ? 'https://sandbox.2checkout.com/checkout/buy'
            : 'https://secure.2checkout.com/checkout/buy';
    }
}
