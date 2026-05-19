<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Phone;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class BakongWebhookController extends Controller
{
    /**
     * Handle Bakong payment webhook
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Bakong webhook received:', $request->all());

        try {
            // Extract data from webhook
            $billNo = $request->input('bill_no') ?? $request->input('referenceId') ?? $request->input('reference_id');
            $transactionId = $request->input('transaction_id') ?? $request->input('transactionId');
            $status = $request->input('status');
            $amount = $request->input('amount');

            if (!$billNo) {
                Log::error('Bakong webhook: No bill number provided');
                return response()->json(['success' => false, 'message' => 'No bill number'], 400);
            }

            // Find the sale
            $sale = Sale::where('bill_no', $billNo)->first();

            if (!$sale) {
                Log::error('Bakong webhook: Sale not found', ['bill_no' => $billNo]);
                return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
            }

            // Check if already completed
            if ($sale->status === 'completed') {
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // Verify payment status
            $isPaid = false;
            if ($status) {
                $statusUpper = strtoupper($status);
                $isPaid = in_array($statusUpper, ['SUCCESS', 'COMPLETED', 'PAID']);
            }

            if ($isPaid) {
                DB::transaction(function () use ($sale, $transactionId) {
                    $sale->lockForUpdate();
                    
                    if ($sale->status !== 'completed') {
                        $sale->update([
                            'status' => 'completed',
                            'transaction_id' => $transactionId
                        ]);
                        
                        // Reduce stock
                        foreach ($sale->items as $saleItem) {
                            $phone = Phone::lockForUpdate()->find($saleItem->phone_id);
                            if ($phone) {
                                $phone->qty -= $saleItem->qty;
                                $phone->save();
                            }
                        }
                    }
                });

                // Send notifications (email + telegram)
                try {
                    $sale->load(['items.phone', 'user']);
                    
                    // Send email notification
                    if ($sale->user && $sale->user->email) {
                        try {
                            Mail::to($sale->user->email)->send(new OrderConfirmation($sale));
                        } catch (\Exception $e) {
                            Log::error('Email error: ' . $e->getMessage());
                        }
                    }

                    // Send Telegram notification
                    try {
                        $telegram = app(TelegramService::class);
                        $telegram->sendPaymentSuccess([
                            'bill_number' => $sale->bill_no,
                            'amount' => $sale->total_price,
                            'currency' => 'USD',
                            'store_label' => env('MERCHANT_NAME', 'PhoneShop'),
                            'mobile_number' => $sale->user->phone ?? 'N/A',
                            'transaction_id' => $transactionId ?? 'N/A'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Telegram notification error: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    Log::error('Notification error: ' . $e->getMessage());
                }

                Log::info('Bakong payment completed', ['bill_no' => $billNo]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'play_sound' => true,
                    'amount' => $sale->total_price,
                    'khmer_text' => 'បានទទួលប្រាក់ ' . number_format($sale->total_price, 2) . ' ដុល្លារ'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not completed',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Bakong webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
