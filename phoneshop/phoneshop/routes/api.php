<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PhoneApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\BakongWebhookController;
use App\Http\Controllers\Api\PaymentNotificationController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\BiometricAuthController;
use App\Http\Controllers\Api\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Bakong webhook (must be public for Bakong to call)
Route::post('/webhook/bakong', [BakongWebhookController::class, 'handleWebhook'])->name('webhook.bakong.payment');

// OTP endpoints (public)
Route::post('/otp/request', [OtpController::class, 'requestOtp']);
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);
Route::post('/otp/resend', [OtpController::class, 'resendOtp']);
Route::get('/otp/stats', [OtpController::class, 'stats'])->middleware('api.key:admin');

// Biometric Authentication endpoints (public)
Route::post('/biometric/check', [BiometricAuthController::class, 'checkBiometric']);
Route::post('/biometric/challenge', [BiometricAuthController::class, 'generateChallenge']);
Route::post('/biometric/verify', [BiometricAuthController::class, 'verifyBiometric']);
Route::post('/biometric/pairing/complete', [BiometricAuthController::class, 'completePairing']);

// Biometric Authentication endpoints (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/biometric/register', [BiometricAuthController::class, 'registerDevice']);
    Route::get('/biometric/devices', [BiometricAuthController::class, 'getDevices']);
    Route::get('/biometric/pairing/qr', [BiometricAuthController::class, 'generatePairingQR']);
    Route::delete('/biometric/devices/{tokenId}', [BiometricAuthController::class, 'revokeDevice']);
});

// Public API endpoints (no authentication required)
Route::get('/products', [PhoneApiController::class, 'index']);
Route::get('/products/{id}', [PhoneApiController::class, 'show']);
Route::get('/categories', [PhoneApiController::class, 'categories']);

// Payment check endpoint (public - no API key required)
Route::get('/payment/check-status/{billNo}', function($billNo) {
    $verifier = app(\App\Services\BakongPaymentVerifier::class);
    return response()->json($verifier->verifyAndProcess($billNo));
});

// Check for new payments (for dashboard notifications)
Route::get('/check-new-payments', [PaymentNotificationController::class, 'checkNewPayments']);

// Manual payment confirmation (for testing)
Route::post('/payment/mark-paid/{billNo}', function($billNo) {
    $verifier = app(\App\Services\BakongPaymentVerifier::class);
    return response()->json($verifier->forceComplete($billNo));
});

// Receipt upload endpoint (public)
Route::post('/upload/receipt', [UploadController::class, 'uploadReceipt']);

// Invoice data endpoint (public)
Route::get('/payment/invoice/{billNo}', function($billNo) {
    $sale = \App\Models\Sale::with('items.phone')->where('bill_no', $billNo)->first();
    
    if (!$sale) {
        return response()->json([
            'success' => false,
            'message' => 'Invoice not found'
        ], 404);
    }
    
    return response()->json([
        'success' => true,
        'invoice' => [
            'bill_no' => $sale->bill_no,
            'customer_name' => $sale->customer_name,
            'customer_email' => $sale->customer_email,
            'total_price' => $sale->total,
            'payment_method' => $sale->payment_method,
            'payment_status' => $sale->payment_status,
            'created_at' => $sale->created_at->format('M d, Y H:i'),
            'items' => $sale->items->map(function($item) {
                return [
                    'phone_name' => $item->phone->name,
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            })
        ]
    ]);
});

// Protected API endpoints (require API key)
Route::middleware('api.key')->group(function () {
    Route::get('/orders', [DashboardApiController::class, 'orders']);
    Route::get('/orders/{id}', [DashboardApiController::class, 'orderDetails']);
    Route::post('/orders', [DashboardApiController::class, 'createOrder']);
    Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
    Route::get('/dashboard/monthly-sales', [DashboardApiController::class, 'monthlySales']);
    
    // Order Approval API
    Route::get('/orders/pending-approval', function() {
        $orders = \App\Models\Sale::with(['items.phone', 'user'])
            ->where('approval_status', 'pending')
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'bill_no' => $order->bill_no,
                    'customer_name' => $order->customer_name ?? $order->user->name ?? 'Guest',
                    'total_price' => $order->total_price,
                    'currency' => $order->currency,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'approval_status' => $order->approval_status,
                    'receipt_url' => $order->receipt_path ? asset('storage/' . $order->receipt_path) : null,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                ];
            })
        ]);
    });
});

// Admin API endpoints (require API key with admin permissions)
Route::middleware(['api.key:admin'])->group(function () {
    Route::post('/products', [PhoneApiController::class, 'store']);
    Route::put('/products/{id}', [PhoneApiController::class, 'update']);
    Route::delete('/products/{id}', [PhoneApiController::class, 'destroy']);
});
