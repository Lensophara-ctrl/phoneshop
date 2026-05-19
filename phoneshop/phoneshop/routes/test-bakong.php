<?php

/**
 * Test Bakong Payment Integration
 * 
 * This route helps test and debug Bakong payments
 * Access: /test-bakong-payment/{billNumber}
 */

use Illuminate\Support\Facades\Route;
use App\Models\Sale;
use App\Services\BakongService;
use Illuminate\Support\Facades\Log;

Route::get('/test-bakong-payment/{billNumber}', function ($billNumber, BakongService $bakongService) {
    $output = [];
    $output[] = "=== Bakong Payment Test ===\n";
    $output[] = "Bill Number: {$billNumber}\n";
    $output[] = "Time: " . now()->toDateTimeString() . "\n\n";
    
    // Find the sale
    $sale = Sale::where('bill_no', $billNumber)->first();
    
    if (!$sale) {
        $output[] = "❌ ERROR: Sale not found for bill number: {$billNumber}\n";
        return response('<pre>' . implode('', $output) . '</pre>');
    }
    
    $output[] = "✅ Sale found:\n";
    $output[] = "   - ID: {$sale->id}\n";
    $output[] = "   - Status: {$sale->status}\n";
    $output[] = "   - Total: \${$sale->total_price}\n";
    $output[] = "   - Payment Method: {$sale->payment_method}\n";
    $output[] = "   - Payment MD5: {$sale->payment_md5}\n\n";
    
    if (!$sale->payment_md5) {
        $output[] = "❌ ERROR: No payment MD5 found. Cannot verify payment.\n";
        return response('<pre>' . implode('', $output) . '</pre>');
    }
    
    // Check payment with Bakong API
    $output[] = "Checking payment with Bakong API...\n";
    $output[] = "MD5: {$sale->payment_md5}\n\n";
    
    try {
        $result = $bakongService->checkPayment($sale->payment_md5);
        
        $output[] = "API Response:\n";
        $output[] = json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        
        $responseCode = $result['responseCode'] ?? -1;
        $status = $result['data']['status'] ?? $result['status'] ?? 'UNKNOWN';
        
        $output[] = "Parsed Values:\n";
        $output[] = "   - Response Code: {$responseCode}\n";
        $output[] = "   - Status: {$status}\n\n";
        
        // Check if payment is successful
        $isSuccess = (
            $responseCode === 0 || 
            $responseCode === '0' ||
            strtoupper($status) === 'SUCCESS' || 
            strtoupper($status) === 'COMPLETED' ||
            strtoupper($status) === 'PAID' ||
            (isset($result['data']['paid']) && $result['data']['paid'] === true)
        );
        
        if ($isSuccess) {
            $output[] = "✅ PAYMENT VERIFIED!\n";
            $output[] = "   The payment has been confirmed by Bakong API.\n\n";
            
            if ($sale->status !== 'completed') {
                $output[] = "⚠️  Sale status is '{$sale->status}' but should be 'completed'.\n";
                $output[] = "   The automatic verification may not have run yet.\n";
            } else {
                $output[] = "✅ Sale status is already 'completed'.\n";
            }
        } else {
            $output[] = "⏳ PAYMENT PENDING\n";
            $output[] = "   The payment has not been confirmed yet.\n";
            $output[] = "   Please complete the payment via Bakong app.\n";
        }
        
    } catch (\Exception $e) {
        $output[] = "❌ ERROR: " . $e->getMessage() . "\n";
        $output[] = "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
    $output[] = "\n=== Test Complete ===\n";
    $output[] = "\nNext Steps:\n";
    $output[] = "1. If payment is pending, scan QR and pay via Bakong app\n";
    $output[] = "2. Wait 3-6 seconds for automatic detection\n";
    $output[] = "3. Or click 'Check Payment' button manually\n";
    $output[] = "4. Check Laravel logs: tail -f storage/logs/laravel.log\n";
    
    return response('<pre>' . implode('', $output) . '</pre>');
});

Route::get('/test-bakong-config', function (BakongService $bakongService) {
    $output = [];
    $output[] = "=== Bakong Configuration Test ===\n\n";
    
    $output[] = "Environment Variables:\n";
    $output[] = "   - BAKONG_API_URL: " . (env('BAKONG_API_URL') ?: '❌ NOT SET') . "\n";
    $output[] = "   - BAKONG_TOKEN: " . (env('BAKONG_TOKEN') ? '✅ SET (' . substr(env('BAKONG_TOKEN'), 0, 20) . '...)' : '❌ NOT SET') . "\n";
    $output[] = "   - MERCHANT_BAKONG_ID: " . (env('MERCHANT_BAKONG_ID') ?: '❌ NOT SET') . "\n";
    $output[] = "   - MERCHANT_NAME: " . (env('MERCHANT_NAME') ?: '❌ NOT SET') . "\n";
    $output[] = "   - MERCHANT_CITY: " . (env('MERCHANT_CITY') ?: '❌ NOT SET') . "\n\n";
    
    $output[] = "Config Values:\n";
    $output[] = "   - services.bakong.api_url: " . (config('services.bakong.api_url') ?: '❌ NOT SET') . "\n";
    $output[] = "   - services.bakong.token: " . (config('services.bakong.token') ? '✅ SET' : '❌ NOT SET') . "\n";
    $output[] = "   - services.bakong.merchant.bakong_id: " . (config('services.bakong.merchant.bakong_id') ?: '❌ NOT SET') . "\n\n";
    
    if (!env('BAKONG_TOKEN')) {
        $output[] = "❌ ERROR: BAKONG_TOKEN is not configured!\n";
        $output[] = "   Please set it in your .env file.\n\n";
    }
    
    if (!env('MERCHANT_BAKONG_ID')) {
        $output[] = "❌ ERROR: MERCHANT_BAKONG_ID is not configured!\n";
        $output[] = "   Please set it in your .env file.\n\n";
    }
    
    if (env('BAKONG_TOKEN') && env('MERCHANT_BAKONG_ID')) {
        $output[] = "✅ Configuration looks good!\n\n";
        $output[] = "Test Payment Generation:\n";
        
        try {
            $testAmount = 0.01;
            $testBillNo = 'TEST-' . time();
            $qrResult = $bakongService->generateKHQR($testAmount, $testBillNo);
            
            if (isset($qrResult['data']['qr'])) {
                $output[] = "   ✅ QR Generation: SUCCESS\n";
                $output[] = "   - QR String: " . substr($qrResult['data']['qr'], 0, 50) . "...\n";
                $output[] = "   - MD5: " . $qrResult['data']['md5'] . "\n";
                $output[] = "   - QR URL: https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrResult['data']['qr']) . "\n";
            } else {
                $output[] = "   ⚠️  QR Generation: Unexpected response\n";
                $output[] = "   Response: " . json_encode($qrResult) . "\n";
            }
        } catch (\Exception $e) {
            $output[] = "   ❌ QR Generation: FAILED\n";
            $output[] = "   Error: " . $e->getMessage() . "\n";
        }
    }
    
    $output[] = "\n=== Test Complete ===\n";
    
    return response('<pre>' . implode('', $output) . '</pre>');
});
