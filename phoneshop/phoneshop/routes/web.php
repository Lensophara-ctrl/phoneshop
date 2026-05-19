<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use Illuminate\Support\Facades\Route;

// Language Switch Route
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/', [FrontendController::class, 'home'])->name('shop.home');
Route::get('/about', [FrontendController::class, 'about'])->name('shop.about');

// Bakong Test Routes (for debugging)
if (config('app.debug')) {
    require __DIR__ . '/test-bakong.php';
}

// Temporary: Check/Create Admin
Route::get('/check-admin', function() {
    $admins = \App\Models\User::where('role', 'admin')->get();
    
    if ($admins->isEmpty()) {
        // Create admin
        $admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
        return "Admin created!<br>Email: admin@gmail.com<br>Password: admin123<br><a href='/admin-login'>Login Now</a>";
    }
    
    $output = "Existing admins:<br>";
    foreach ($admins as $admin) {
        $output .= "Email: {$admin->email}<br>";
    }
    $output .= "<br>Try password: admin123 or password<br>";
    $output .= "<a href='/admin-login'>Go to Login</a>";
    return $output;
});

Route::get('/admin-login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin-login', [AuthController::class, 'adminLogin']);

// Modern Login Routes
Route::get('/login', [AuthController::class, 'showModernLogin'])->name('login');
Route::get('/login/email', [AuthController::class, 'showEmailLogin'])->name('login.email');
Route::get('/login/otp', [AuthController::class, 'showOtpLogin'])->name('login.otp');
Route::get('/login/biometric', [AuthController::class, 'showBiometricLogin'])->name('login.biometric');
Route::post('/login', [AuthController::class, 'customerLogin'])->name('customer.login');

// Social Login Routes
Route::get('/login/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/login/facebook', [AuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
Route::get('/login/apple', [AuthController::class, 'redirectToApple'])->name('login.apple');
Route::get('/login/apple/callback', [AuthController::class, 'handleAppleCallback']);

// OTP Web Routes
Route::post('/otp/request-web', [AuthController::class, 'requestOtpWeb'])->name('otp.request.web');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'customerRegister'])->name('customer.register');

// OTP Verification Routes
Route::get('/otp/verify', [AuthController::class, 'showOtpVerify'])->name('otp.verify');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');
Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Add to cart - requires authentication
Route::get('/add-to-cart/{id}', [FrontendController::class, 'addToCart'])->name('shop.add');

// Public routes
Route::get('/search', [FrontendController::class, 'search'])->name('shop.search');
Route::get('/category/{id}', [FrontendController::class, 'category'])->name('shop.category');
Route::get('/product/{id}', [FrontendController::class, 'product'])->name('shop.product');
Route::get('/cart', [FrontendController::class, 'cart'])->name('shop.cart');
Route::get('/track-order', [FrontendController::class, 'trackOrder'])->name('shop.track');

// Live Chat - Public routes
Route::post('/livechat/start', [LiveChatController::class, 'start'])->name('livechat.start');
Route::post('/livechat/{conversation}/send', [LiveChatController::class, 'send'])->name('livechat.send');
Route::get('/livechat/{conversation}/messages', [LiveChatController::class, 'messages'])->name('livechat.messages');

// Authenticated routes - requires login
Route::middleware(['auth'])->group(function () {
    Route::get('/remove-from-cart/{id}', [FrontendController::class, 'removeFromCart'])->name('shop.remove');
    Route::post('/update-cart', [FrontendController::class, 'updateCart'])->name('shop.update-cart');
    Route::get('/checkout', [FrontendController::class, 'checkout'])->name('shop.checkout');
    Route::post('/checkout/payment-method', [FrontendController::class, 'showPaymentMethod'])->name('shop.payment.method');
    Route::post('/checkout/payment', [FrontendController::class, 'processPayment'])->name('shop.payment.process');
    Route::get('/checkout/success/{billNo}', [FrontendController::class, 'success'])->name('shop.success');
    Route::get('/checkout/return', [FrontendController::class, 'handleCheckoutReturn'])->name('shop.checkout.return');

    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [UserProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/update-image', [UserProfileController::class, 'updateProfileImage'])->name('profile.update-image');
    
    // Customer Orders
    Route::get('/my-orders', [\App\Http\Controllers\CustomerOrderController::class, 'index'])->name('customer.orders');
    Route::post('/customer/orders/{order}/lock', [\App\Http\Controllers\CustomerOrderController::class, 'lockOrder'])->name('customer.order.lock');
    Route::get('/customer/orders/{order}/invoice', [\App\Http\Controllers\CustomerOrderController::class, 'downloadInvoice'])->name('customer.order.invoice');
});

// Admin routes - requires admin or staff role
Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
    
    // Test Full Interface
    Route::get('/test-interface', function() {
        return view('test-full-interface');
    })->name('test.interface');

    Route::resource('categories', CategoryController::class);
    Route::resource('phones', PhoneController::class);
    Route::get('phones-debug-upload', [PhoneController::class, 'debugUpload'])->name('phones.debug-upload');
    Route::post('phones-debug-upload-test', [PhoneController::class, 'debugUploadTest'])->name('phones.debug-upload-test');
    Route::resource('slides', SlideController::class);
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('sales/latest', [SaleController::class, 'latest'])->name('sales.latest');
    Route::get('sales/{sale}/generate-qr', [SaleController::class, 'generateQR'])->name('sales.generate-qr');
    Route::get('sales/{sale}/check-status', [SaleController::class, 'checkStatus'])->name('sales.check-status');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create-admin', [UserController::class, 'createAdmin'])->name('users.create-admin');
    Route::post('users/create-admin', [UserController::class, 'storeAdmin'])->name('users.store-admin');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // API Key Management
    Route::get('api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
    Route::post('api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggle'])->name('api-keys.toggle');
    
    // Delivery Management
    Route::get('deliveries', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('deliveries/map', [\App\Http\Controllers\DeliveryController::class, 'map'])->name('deliveries.map');
    Route::get('deliveries/{sale}', [\App\Http\Controllers\DeliveryController::class, 'show'])->name('deliveries.show');
    Route::put('deliveries/{sale}', [\App\Http\Controllers\DeliveryController::class, 'update'])->name('deliveries.update');
    
    // Order Approval Management
    Route::get('orders/approval', [\App\Http\Controllers\OrderApprovalController::class, 'index'])->name('orders.approval');
    Route::get('orders/approval/stats', [\App\Http\Controllers\OrderApprovalController::class, 'stats'])->name('orders.approval.stats');
    Route::get('orders/{order}/details', [\App\Http\Controllers\OrderApprovalController::class, 'show'])->name('orders.details');
    Route::post('orders/{order}/approve', [\App\Http\Controllers\OrderApprovalController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [\App\Http\Controllers\OrderApprovalController::class, 'reject'])->name('orders.reject');
    
    // Reports
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales-summary', [\App\Http\Controllers\ReportController::class, 'salesSummary'])->name('reports.sales-summary');
    Route::get('reports/top-selling-products', [\App\Http\Controllers\ReportController::class, 'topSellingProducts'])->name('reports.top-selling');
    Route::get('reports/revenue-by-category', [\App\Http\Controllers\ReportController::class, 'revenueByCategory'])->name('reports.revenue-category');
    Route::get('reports/daily-sales', [\App\Http\Controllers\ReportController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('reports/customer-report', [\App\Http\Controllers\ReportController::class, 'customerReport'])->name('reports.customers');
    Route::get('reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('reports/payment-methods', [\App\Http\Controllers\ReportController::class, 'paymentMethodReport'])->name('reports.payment-methods');
    Route::get('reports/monthly-comparison', [\App\Http\Controllers\ReportController::class, 'monthlyComparison'])->name('reports.monthly-comparison');
    Route::get('reports/order-status', [\App\Http\Controllers\ReportController::class, 'orderStatusReport'])->name('reports.order-status');
    Route::get('reports/profit-analysis', [\App\Http\Controllers\ReportController::class, 'profitAnalysis'])->name('reports.profit-analysis');
    
    // Customer Report Lock & Invoice
    Route::post('reports/customer/{customer}/lock', [\App\Http\Controllers\ReportController::class, 'lockCustomerReport'])->name('reports.customer.lock');
    Route::get('reports/customer/{customer}/invoice', [\App\Http\Controllers\ReportController::class, 'downloadCustomerInvoice'])->name('reports.customer.invoice');
    Route::get('reports/daily-invoice/{date}', [\App\Http\Controllers\ReportController::class, 'downloadDailyInvoice'])->name('reports.daily.invoice');

    // Live Chat - Admin
    Route::get('chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::get('chat/{conversation}', [AdminChatController::class, 'show'])->name('admin.chat.show');
    Route::post('chat/{conversation}/reply', [AdminChatController::class, 'reply'])->name('admin.chat.reply');
    Route::post('chat/{conversation}/mark-read', [AdminChatController::class, 'markRead'])->name('admin.chat.mark-read');
    Route::post('chat/{conversation}/close', [AdminChatController::class, 'close'])->name('admin.chat.close');
    Route::post('chat/{conversation}/resolve', [AdminChatController::class, 'resolve'])->name('admin.chat.resolve');
    Route::post('chat/{conversation}/reopen', [AdminChatController::class, 'reopen'])->name('admin.chat.reopen');
    Route::get('chat-poll', [AdminChatController::class, 'poll'])->name('admin.chat.poll');
});

Route::prefix('test')->group(function () {
    Route::get('/telegram', [TestController::class, 'testTelegramNotification'])->name('test.telegram');
    Route::get('/telegram-demo', function() {
        return view('test-telegram-notification');
    })->name('test.telegram.demo');
    Route::get('/payment-endpoints', [TestController::class, 'testPaymentEndpoint'])->name('test.endpoints');
    Route::get('/database-status', [TestController::class, 'testDatabaseStatus'])->name('test.database');
    Route::get('/phone-images', [TestController::class, 'testPhoneImages'])->name('test.phone.images');
    Route::get('/otp', function() {
        return view('test-otp');
    })->name('test.otp');
    Route::get('/biometric', function() {
        return view('test-biometric');
    })->name('test.biometric');
});

// Include test payment routes in debug mode
if (config('app.debug')) {
    require __DIR__ . '/test-payment.php';
}

Route::get('/upload-aba-qr', function() {
    return response(
        file_get_contents(public_path('upload-aba-qr.html')),
        200,
        ['Content-Type' => 'text/html; charset=UTF-8']
    );
})->name('upload.aba.qr');
