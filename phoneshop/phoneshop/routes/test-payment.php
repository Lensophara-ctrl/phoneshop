<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;
use App\Services\BakongService;

// Test payment system diagnostics
Route::get('/test/payment-system', function() {
    $output = [];
    
    // 1. Check Bakong configuration
    $output[] = "=== BAKONG CONFIGURATION ===";
    $output[] = "API URL: " . (config('services.bakong.api_url') ?? env('BAKONG_API_URL', 'NOT SET'));
    $output[] = "Token: " . (env('BAKONG_TOKEN') ? 'SET (length: ' . strlen(env('BAKONG_TOKEN')) . ')' : 'NOT SET');
    $output[] = "Merchant ID: " . (env('MERCHANT_BAKONG_ID') ?? 'NOT SET');
    $output[] = "";
    
    // 2. Check latest pending payment
    $output[] = "=== LATEST PENDING PAYMENT ===";
    $pendingSale = Sale::where('status', 'pending')
        ->where('payment_method', 'bakong')
        ->latest()
        ->first();
    
    if ($pendingSale) {
        $output[] = "Bill No: " . $pendingSale->bill_no;
        $output[] = "Amount: $" . $pendingSale->total_price;
        $output[] = "Payment MD5: " . ($pendingSale->payment_md5 ?? 'NOT SET');
        $output[] = "Created: " . $pendingSale->created_at;
        $output[] = "";
        
        // 3. Try to check payment with Bakong API
        if ($pendingSale->payment_md5) {
            $output[] = "=== CHECKING WITH BAKONG API ===";
            try {
                $bakongService = app(BakongService::class);
                $result = $bakongService->checkPayment($pendingSale->payment_md5);
                
                $output[] = "Response Code: " . ($result['responseCode'] ?? 'N/A');
                $output[] = "Response Message: " . ($result['responseMessage'] ?? 'N/A');
                $output[] = "Status: " . ($result['data']['status'] ?? 'N/A');
                $output[] = "Full Response: " . json_encode($result, JSON_PRETTY_PRINT);
            } catch (\Exception $e) {
                $output[] = "ERROR: " . $e->getMessage();
            }
        } else {
            $output[] = "ERROR: No payment MD5 found!";
        }
    } else {
        $output[] = "No pending Bakong payments found";
    }
    
    $output[] = "";
    $output[] = "=== RECOMMENDATIONS ===";
    
    if (!env('BAKONG_TOKEN')) {
        $output[] = "❌ Set BAKONG_TOKEN in .env file";
    } else {
        $output[] = "✅ Bakong token is set";
    }
    
    if (!env('MERCHANT_BAKONG_ID')) {
        $output[] = "❌ Set MERCHANT_BAKONG_ID in .env file";
    } else {
        $output[] = "✅ Merchant Bakong ID is set";
    }
    
    if ($pendingSale && !$pendingSale->payment_md5) {
        $output[] = "❌ Payment MD5 not generated - check QR generation";
    }
    
    return response('<pre>' . implode("\n", $output) . '</pre>');
});

// Force complete a payment (for testing)
Route::get('/test/complete-payment/{billNo}', function($billNo) {
    $sale = Sale::where('bill_no', $billNo)->first();
    
    if (!$sale) {
        return response()->json(['error' => 'Sale not found'], 404);
    }
    
    if ($sale->status === 'completed') {
        return response()->json(['message' => 'Already completed']);
    }
    
    DB::transaction(function () use ($sale) {
        $sale->lockForUpdate();
        $sale->update(['status' => 'completed']);
        
        // Reduce stock
        foreach ($sale->items as $saleItem) {
            $phone = \App\Models\Phone::lockForUpdate()->find($saleItem->phone_id);
            if ($phone) {
                $phone->qty -= $saleItem->qty;
                $phone->save();
            }
        }
    });
    
    // Send notifications
    try {
        $sale->load(['items.phone', 'user']);
        
        if ($sale->user && $sale->user->email) {
            \Mail::to($sale->user->email)->send(new \App\Mail\OrderConfirmation($sale));
        }
        
        $telegramService = app(\App\Services\TelegramService::class);
        $telegramService->sendPaymentNotification([
            'bill_no' => $sale->bill_no,
            'amount' => $sale->total_price,
            'currency' => $sale->currency ?? 'USD',
            'payment_method' => 'Bakong (Test)',
            'status' => 'completed',
            'customer_name' => $sale->user->name ?? 'Guest',
            'items_count' => $sale->items->count(),
            'items' => $sale->items->map(function($item) {
                return [
                    'name' => $item->phone->name,
                    'qty' => $item->qty,
                    'price' => $item->price
                ];
            })->toArray()
        ]);
    } catch (\Exception $e) {
        Log::error('Notification error: ' . $e->getMessage());
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Payment completed successfully',
        'bill_no' => $billNo,
        'redirect' => route('shop.success', $billNo)
    ]);
});
