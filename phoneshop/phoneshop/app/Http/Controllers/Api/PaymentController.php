<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Phone;
use App\Models\SaleItem;
use App\Services\KHQRService;
use App\Services\BakongService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected KHQRService $khqrService;
    protected BakongService $bakongService;
    protected TelegramService $telegramService;

    public function __construct(KHQRService $khqrService, BakongService $bakongService, TelegramService $telegramService)
    {
        $this->khqrService = $khqrService;
        $this->bakongService = $bakongService;
        $this->telegramService = $telegramService;
    }

    /**
     * Check payment status - verifies with Bakong API
     * Now allows unauthenticated access for easier payment checking
     */
    public function checkStatus($billNumber)
    {
        try {
            // First check if sale exists and is completed (no auth required)
            $sale = Sale::where('bill_no', $billNumber)->first();

            if (! $sale) {
                return response()->json([
                    'paid' => false,
                    'message' => 'Payment not found',
                ]);
            }

            // If already completed, return success
            if ($sale->status === 'completed') {
                return response()->json([
                    'paid' => true,
                    'bill_no' => $sale->bill_no,
                    'total_price' => $sale->total_price,
                    'created_at' => $sale->created_at,
                    'status' => $sale->status,
                ]);
            }

            // Sale exists but not completed - load items
            $sale->load('items', 'items.phone');
            
            // Get the stored payment MD5 for verification
            $paymentMd5 = $sale->payment_md5;
            
            // If we have a stored payment MD5, use it for verification
            if ($paymentMd5) {
                // Check payment with Bakong API using stored MD5
                $bakongResult = $this->bakongService->checkPayment($paymentMd5);
                
                Log::info('Bakong payment check result (using stored MD5):', $bakongResult);

                // Check if payment is successful
                // Bakong API may return different response formats
                $responseCode = $bakongResult['responseCode'] ?? -1;
                $status = $bakongResult['data']['status'] ?? $bakongResult['status'] ?? 'PENDING';
                
                // Log the exact response for debugging
                Log::info('Payment verification details:', [
                    'bill_no' => $billNumber,
                    'responseCode' => $responseCode,
                    'status' => $status,
                    'full_response' => $bakongResult
                ]);
                
                // Check multiple success indicators
                $isSuccess = (
                    $responseCode === 0 || 
                    $responseCode === '0' ||
                    strtoupper($status) === 'SUCCESS' || 
                    strtoupper($status) === 'COMPLETED' ||
                    strtoupper($status) === 'PAID' ||
                    (isset($bakongResult['data']['paid']) && $bakongResult['data']['paid'] === true)
                );
                
                if ($isSuccess) {
                    // Update sale status to completed (use DB transaction to prevent race condition)
                    \DB::transaction(function () use ($sale) {
                        // Re-fetch sale with lock to prevent race condition
                        $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->first();
                        
                        if ($lockedSale && $lockedSale->status !== 'completed') {
                            $lockedSale->update(['status' => 'completed']);
                            
                            // Decrement stock for each item
                            foreach ($lockedSale->items as $item) {
                                $phone = Phone::lockForUpdate()->find($item->phone_id);
                                if ($phone && $phone->qty >= $item->qty) {
                                    $phone->decrement('qty', $item->qty);
                                }
                            }
                            
                            // Send Telegram notification
                            $user = $lockedSale->user;
                            $totalAmount = $lockedSale->total_price;
                            
                            $itemsList = '';
                            foreach ($lockedSale->items as $item) {
                                $phoneName = $item->phone ? $item->phone->name : 'Unknown Item';
                                $itemsList .= "- {$phoneName} x{$item->qty} = $" . number_format($item->subtotal, 2) . "\n";
                            }

                            $message = "<b>💰 Bakong Payment Confirmed!</b>\n\n";
                            $message .= "<b>Bill No:</b> #{$lockedSale->bill_no}\n";
                            $message .= "<b>Customer:</b> {$user->name}\n";
                            $message .= "<b>Email:</b> {$user->email}\n";
                            $message .= "<b>Total:</b> $" . number_format($totalAmount, 2) . "\n\n";
                            $message .= "<b>Items:</b>\n{$itemsList}";
                            $message .= "<b>Status:</b> Payment Confirmed";

                            $this->telegramService->sendMessage($message);
                        }
                    });
                    
                    // Reload sale to get updated status
                    $sale->refresh();

                    return response()->json([
                        'paid' => true,
                        'bill_no' => $sale->bill_no,
                        'total_price' => $sale->total_price,
                        'created_at' => $sale->created_at,
                        'status' => 'completed',
                    ]);
                }
            }

            // No stored MD5 or payment not confirmed - return pending
            return response()->json([
                'paid' => false,
                'message' => 'Payment pending - please complete payment via Bakong app',
                'bill_no' => $sale->bill_no,
                'status' => $sale->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment check error: ' . $e->getMessage());
            
            // If sale exists with pending status, still return pending
            $sale = Sale::where('bill_no', $billNumber)->first();
                
            if ($sale) {
                return response()->json([
                    'paid' => false,
                    'message' => 'Checking payment status...',
                    'bill_no' => $billNumber,
                    'status' => $sale->status,
                ]);
            }
            
            return response()->json([
                'paid' => false,
                'message' => 'Payment not found',
            ], 500);
        }
    }

    /**
     * Get invoice data for printing (no auth required)
     */
    public function getInvoiceData($billNumber)
    {
        try {
            $sale = Sale::with(['items.phone', 'items.phone.category', 'user'])
                ->where('bill_no', $billNumber)
                ->first();

            if (! $sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found',
                ], 404);
            }

            // Build invoice data
            $items = [];
            foreach ($sale->items as $item) {
                $items[] = [
                    'id' => $item->id,
                    'phone_id' => $item->phone_id,
                    'phone_name' => $item->phone ? $item->phone->name : 'Unknown Item',
                    'category' => $item->phone && $item->phone->category ? $item->phone->category->name : 'N/A',
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            }

            return response()->json([
                'success' => true,
                'invoice' => [
                    'bill_no' => $sale->bill_no,
                    'date' => $sale->created_at->format('M d, Y'),
                    'customer' => [
                        'name' => $sale->user ? $sale->user->name : 'Guest',
                        'email' => $sale->user ? $sale->user->email : 'N/A',
                    ],
                    'items' => $items,
                    'subtotal' => $sale->subtotal,
                    'tax' => $sale->tax,
                    'total_price' => $sale->total_price,
                    'payment_method' => $sale->payment_method,
                    'status' => $sale->status,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get invoice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invoice',
            ], 500);
        }
    }

    /**
     * Generate KHQR for payment
     */
    public function generateQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'in:USD,KHR',
            'bill_number' => 'nullable|string',
            'mobile_number' => 'nullable|string',
            'store_label' => 'nullable|string',
            'terminal_label' => 'nullable|string',
            'type' => 'in:individual,merchant',
        ]);

        $type = $validated['type'] ?? 'individual';

        $result = $type === 'merchant'
            ? $this->khqrService->generateMerchantQR($validated)
            : $this->khqrService->generateIndividualQR($validated);

        if (isset($result['data'])) {
            return response()->json([
                'success' => true,
                'qr_code' => $result['data']['qr'] ?? null,
                'md5' => $result['data']['md5'] ?? null,
                'message' => 'QR generated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to generate QR',
        ], 400);
    }

    /**
     * Check payment status via Bakong API
     */
    public function checkPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'md5' => 'required|string',
        ]);

        $result = $this->khqrService->checkPayment($validated['md5']);

        // Check if payment is successful
        $status = $result['data']['status'] ?? $result['responseMessage'] ?? 'PENDING';
        
        if ($status === 'SUCCESS' || (isset($result['responseCode']) && $result['responseCode'] === 0)) {
            // Send Telegram notification
            $this->telegramService->sendPaymentSuccess([
                'amount' => $result['data']['amount'] ?? $request->input('amount', 0),
                'currency' => $result['data']['currency'] ?? 'USD',
                'bill_number' => $result['data']['bill_number'] ?? 'N/A',
                'store_label' => $result['data']['store_label'] ?? 'N/A',
                'mobile_number' => $result['data']['mobile_number'] ?? 'N/A',
                'transaction_id' => $result['data']['hash'] ?? $result['data']['transaction_id'] ?? 'N/A',
            ]);

            return response()->json([
                'success' => true,
                'status' => 'SUCCESS',
                'message' => 'Payment completed successfully!',
                'data' => $result['data'] ?? $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $result['responseMessage'] ?? 'Payment not yet completed',
            'raw' => $result,
        ]);
    }

    /**
     * Verify QR code
     */
    public function verifyQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $result = $this->khqrService->verifyQR($validated['qr_code']);

        return response()->json([
            'success' => $result['data']['valid'] ?? false,
            'message' => $result['data']['valid'] ? 'QR is valid' : 'QR is invalid',
        ]);
    }

    /**
     * Decode QR code
     */
    public function decodeQR(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $result = $this->khqrService->decodeQR($validated['qr_code']);

        return response()->json([
            'success' => isset($result['data']),
            'data' => $result['data'] ?? null,
        ]);
    }

    /**
     * Generate deep link
     */
    public function generateDeepLink(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'app_name' => 'nullable|string',
            'app_icon_url' => 'nullable|url',
            'callback_url' => 'nullable|url',
        ]);

        $result = $this->khqrService->generateDeepLink($validated['qr_code'], $validated);

        if (isset($result['data']['shortLink'])) {
            return response()->json([
                'success' => true,
                'deep_link' => $result['data']['shortLink'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to generate deep link',
        ], 400);
    }

    // =====================================================
    // BAKONG CHECKOUT API METHODS
    // =====================================================

    /**
     * Create Bakong Checkout Session (API)
     */
    public function createCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'bill_number' => 'nullable|string',
            'currency' => 'in:USD,KHR',
        ]);

        $result = $this->bakongService->createCheckoutSession([
            'amount' => $validated['amount'],
            'bill_number' => $validated['bill_number'] ?? uniqid('INV-'),
            'currency' => $validated['currency'] ?? 'USD',
            'description' => 'Payment for Order ' . ($validated['bill_number'] ?? ''),
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'checkout_url' => $result['checkout_url'],
                'token' => $result['token'],
                'reference_id' => $result['reference_id'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to create checkout session',
        ], 400);
    }

    /**
     * Check Bakong Checkout Payment Status (API)
     */
    public function checkCheckoutStatus(string $token): JsonResponse
    {
        $result = $this->bakongService->checkCheckoutStatus($token);

        return response()->json([
            'success' => $result['success'] ?? false,
            'paid' => $result['paid'] ?? false,
            'status' => $result['status'] ?? 'UNKNOWN',
            'amount' => $result['amount'] ?? 0,
            'transaction_id' => $result['transaction_id'] ?? null,
            'error' => $result['error'] ?? null,
        ]);
    }
}
