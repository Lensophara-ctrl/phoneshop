<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\BakongService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    public function home(Request $request)
    {
        $categories = Category::all();
        $slides = \App\Models\Slide::where('is_active', true)->orderBy('order')->get();
        $settings = \App\Helpers\SettingsHelper::getAllForView();

        $sort = $request->get('sort', 'latest');
        $search = $request->get('search', '');

        $query = Phone::query();

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('id', 'like', '%'.$search.'%');
        }

        match ($sort) {
            'price-low' => $query->orderBy('price', 'asc'),
            'price-high' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $phones = $query->get();

        // Get top selling products
        $topSellingProducts = Phone::select('phones.*', DB::raw('SUM(sale_items.qty) as total_sold'))
            ->join('sale_items', 'phones.id', '=', 'sale_items.phone_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->groupBy('phones.id')
            ->orderBy('total_sold', 'desc')
            ->limit(8)
            ->get();

        return view('frontend.home', compact('categories', 'phones', 'slides', 'sort', 'search', 'topSellingProducts', 'settings'));
    }

    public function about()
    {
        $settings = \App\Helpers\SettingsHelper::getAllForView();
        return view('frontend.about', compact('settings'));
    }


    public function search(Request $request)
    {
        $categories = Category::all();
        $settings = \App\Helpers\SettingsHelper::getAllForView();
        $search = $request->get('q', '');
        $sort = $request->get('sort', 'latest');

        $query = Phone::query();

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('id', 'like', '%'.$search.'%');
        }

        match ($sort) {
            'price-low' => $query->orderBy('price', 'asc'),
            'price-high' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $phones = $query->get();

        return view('frontend.search', compact('categories', 'phones', 'search', 'sort', 'settings'));
    }

    public function checkout(BakongService $bakongService)
    {
        // Only customers can checkout
        if (Auth::user()->role !== 'customer') {
            return redirect()->route('shop.home')->with('error', 'Only customers can place orders.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        // Show customer information form
        return view('frontend.checkout', compact('cart'));
    }

    /**
     * Show payment method selection after customer info is collected
     */
    public function showPaymentMethod(Request $request)
    {
        // Validate customer information
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'order_notes' => 'nullable|string|max:1000',
        ]);

        // Store customer info in session
        session(['customer_info' => $validated]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        $totalAmount = 0;
        foreach ($cart as $details) {
            $totalAmount += $details['price'] * $details['qty'];
        }

        return view('frontend.payment-method', compact('cart', 'totalAmount'));
    }

    /**
     * Create a pending sale record
     */
    private function createPendingSale(array $cart, string $billNumber, float $totalAmount, string $paymentMethod, ?string $paymentMd5 = null): void
    {
        $userId = auth()->id();
        $customerInfo = session('customer_info', []);

        DB::transaction(function () use ($cart, $billNumber, $totalAmount, $paymentMethod, $paymentMd5, $userId, $customerInfo) {
            $saleData = [
                'bill_no' => $billNumber,
                'user_id' => $userId,
                'customer_name' => $customerInfo['customer_name'] ?? null,
                'customer_email' => $customerInfo['customer_email'] ?? null,
                'customer_phone' => $customerInfo['customer_phone'] ?? null,
                'customer_address' => $customerInfo['customer_address'] ?? null,
                'customer_city' => $customerInfo['customer_city'] ?? null,
                'customer_postal_code' => $customerInfo['customer_postal_code'] ?? null,
                'delivery_latitude' => $customerInfo['delivery_latitude'] ?? null,
                'delivery_longitude' => $customerInfo['delivery_longitude'] ?? null,
                'delivery_status' => 'pending',
                'order_notes' => $customerInfo['order_notes'] ?? null,
                'subtotal' => $totalAmount,
                'tax' => 0,
                'total_price' => $totalAmount,
                'payment_method' => $paymentMethod,
                'status' => 'pending', // Start as pending
            ];

            // Add payment MD5 if provided (for KHQR verification)
            if ($paymentMd5) {
                $saleData['payment_md5'] = $paymentMd5;
            }

            $sale = Sale::create($saleData);

            $itemsList = '';
            foreach ($cart as $id => $details) {
                $phone = Phone::lockForUpdate()->findOrFail($id);

                $subtotal = $details['price'] * $details['qty'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'phone_id' => $id,
                    'qty' => $details['qty'],
                    'price' => $details['price'],
                    'subtotal' => $subtotal,
                ]);

                $itemsList .= "- {$phone->name} x{$details['qty']} = $" . number_format($subtotal, 2) . "\n";
            }

            // No notification here - only send when payment is confirmed via webhook
        });
    }

    public function processPayment(Request $request, BakongService $bakongService)
    {
        // Only customers can process payment
        if (! Auth::check() || Auth::user()->role !== 'customer') {
            return redirect()->route('shop.home')->with('error', 'Only customers can place orders.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        $paymentType = $request->input('payment_type');
        $totalAmount = 0;
        foreach ($cart as $details) {
            $totalAmount += $details['price'] * $details['qty'];
        }

        // Generate bill number
        $billNumber = strtoupper(uniqid('INV-'));
        session(['pending_bill_no' => $billNumber]);

        try {
            if ($paymentType === 'checkout') {
                // Bakong Checkout (Real) - Redirect to Bakong
                return $this->processBakongCheckout($cart, $billNumber, $totalAmount, $bakongService);
            } elseif ($paymentType === 'cash') {
                // Cash Payment - Process immediately
                return $this->processCashPayment($cart, $billNumber, $totalAmount);
            } else {
                // Bakong Standard (KHQR) - Show QR code
                return $this->processBakongStandard($cart, $billNumber, $totalAmount, $bakongService);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment system error. Please try again.');
        }
    }

    private function processBakongStandard($cart, $billNumber, $totalAmount, BakongService $bakongService)
    {
        // Generate KHQR (QR Code scanning)
        $khqrResult = $bakongService->generateKHQR($totalAmount, $billNumber);
        
        // Extract QR string and MD5 from result
        $khqr = is_array($khqrResult) && isset($khqrResult['data']['qr']) 
            ? $khqrResult['data']['qr'] 
            : $khqrResult;
            
        // Get the MD5 hash for payment verification
        $paymentMd5 = is_array($khqrResult) && isset($khqrResult['data']['md5']) 
            ? $khqrResult['data']['md5'] 
            : null;
        
        // Create pending sale with payment MD5 for verification
        $this->createPendingSale($cart, $billNumber, $totalAmount, 'bakong', $paymentMd5);

        return view('frontend.payment', compact('cart', 'totalAmount', 'khqr', 'billNumber'));
    }

    private function processBakongCheckout($cart, $billNumber, $totalAmount, BakongService $bakongService)
    {
        // Check if Bakong Checkout is enabled
        if (!$bakongService->isCheckoutEnabled()) {
            return redirect()->back()->with('error', 'Bakong Checkout is not enabled. Please use Standard payment.');
        }

        // Create Bakong Checkout session
        $checkoutResult = $bakongService->createCheckoutSession([
            'amount' => $totalAmount,
            'bill_number' => $billNumber,
            'currency' => 'USD',
            'description' => 'Payment for Order ' . $billNumber,
        ]);

        if ($checkoutResult['success'] && !empty($checkoutResult['checkout_url'])) {
            // Store checkout token for status checking
            session(['checkout_token' => $checkoutResult['token'] ?? null]);
            
            // Create pending sale (checkout mode - no MD5 needed)
            $this->createPendingSale($cart, $billNumber, $totalAmount, 'bakong_checkout', null);
            
            // Redirect to Bakong Checkout
            return redirect($checkoutResult['checkout_url']);
        } else {
            // Fallback to Standard QR if checkout fails
            \Illuminate\Support\Facades\Log::warning('Bakong Checkout failed, falling back to Standard: ' . ($checkoutResult['error'] ?? 'Unknown error'));
            return $this->processBakongStandard($cart, $billNumber, $totalAmount, $bakongService);
        }
    }

    private function processBakongPayment($billNumber, BakongService $bakongService)
    {
        // Check if payment was actually made (Sale record exists for this bill)
        $sale = Sale::with(['items', 'items.phone'])
            ->where('bill_no', $billNumber)
            ->where('user_id', auth()->id())
            ->first();

        if (! $sale) {
            return redirect()->back()->with('error', 'Payment not verified. Please complete the payment first.');
        }

        // If already completed, don't process again
        if ($sale->status === 'completed') {
            return redirect()->route('shop.success', $billNumber)->with('success', 'Payment already confirmed.');
        }

        // Verify payment with Bakong API using stored payment_md5
        $khqrService = app(\App\Services\KHQRService::class);
        $paymentMd5 = $sale->payment_md5;
        
        // Also check with checkout token if available
        $checkoutToken = session('checkout_token');
        
        $paymentVerified = false;
        
        // First try checkout token verification
        if ($checkoutToken) {
            $checkoutStatus = $bakongService->checkCheckoutStatus($checkoutToken);
            if ($checkoutStatus['success'] && $checkoutStatus['paid']) {
                $paymentVerified = true;
            }
        }
        
        // If not verified via checkout, try KHQR MD5 verification
        if (!$paymentVerified && $paymentMd5) {
            $bakongResult = $bakongService->checkPayment($paymentMd5);
            
            // Log the exact response for debugging
            Log::info('Bakong payment verification result:', [
                'bill_no' => $billNumber,
                'payment_md5' => $paymentMd5,
                'response' => $bakongResult
            ]);
            
            $responseCode = $bakongResult['responseCode'] ?? -1;
            $status = $bakongResult['data']['status'] ?? $bakongResult['status'] ?? 'PENDING';
            
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
                $paymentVerified = true;
            }
        }
        
        // If payment not verified, redirect back with error
        if (!$paymentVerified) {
            return redirect()->back()->with('error', 'Payment not yet confirmed. Please complete payment via Bakong app and try again.');
        }

        // Payment verified - update sale status to completed with transaction lock
        DB::transaction(function () use ($sale) {
            // Re-fetch with lock to prevent race condition
            $lockedSale = Sale::where('id', $sale->id)->lockForUpdate()->first();
            
            if ($lockedSale && $lockedSale->status !== 'completed') {
                $lockedSale->update(['status' => 'completed']);
                
                // Decrement the stock for each item
                foreach ($lockedSale->items as $item) {
                    $phone = Phone::lockForUpdate()->find($item->phone_id);
                    if ($phone && $phone->qty >= $item->qty) {
                        $phone->decrement('qty', $item->qty);
                    }
                }
            }
        });
        
        // Reload sale with items for notification
        $sale->refresh();
        $sale->load('items.phone');

        // Send Telegram Notification
        $user = auth()->user();
        $totalAmount = $sale->total_price;
        
        $itemsList = '';
        foreach ($sale->items as $item) {
            $phoneName = $item->phone ? $item->phone->name : 'Unknown Item';
            $itemsList .= "- {$phoneName} x{$item->qty} = $" . number_format($item->subtotal, 2) . "\n";
        }

        $message = "<b>💰 Bakong Payment Confirmed!</b>\n\n";
        $message .= "<b>Bill No:</b> #{$billNumber}\n";
        $message .= "<b>Customer:</b> {$user->name}\n";
        $message .= "<b>Email:</b> {$user->email}\n";
        $message .= "<b>Total:</b> $" . number_format($totalAmount, 2) . "\n\n";
        $message .= "<b>Items:</b>\n{$itemsList}";
        $message .= "<b>Status:</b> ✅ Payment Confirmed\n";
        $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

        // Use TelegramService
        $telegramService = app(\App\Services\TelegramService::class);
        $telegramService->sendMessage($message);

        session()->forget(['cart', 'pending_bill_no', 'checkout_token']);

        return redirect()->route('shop.success', $billNumber)->with('success', 'Payment confirmed! Thank you for your purchase.');
    }

    private function processCardPayment(Request $request, $billNumber, BakongService $bakongService)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        try {
            $totalAmount = 0;
            $itemsSummary = '';
            $userId = auth()->id();

            DB::transaction(function () use ($cart, $billNumber, $userId, &$totalAmount, &$itemsSummary) {
                $sale = Sale::create([
                    'bill_no' => $billNumber,
                    'user_id' => $userId,
                    'subtotal' => 0, // Placeholder
                    'tax' => 0,
                    'total_price' => 0, // Placeholder
                    'payment_method' => 'card',
                    'status' => 'completed',
                ]);

                foreach ($cart as $id => $details) {
                    $phone = Phone::lockForUpdate()->findOrFail($id);

                    if ($phone->qty < $details['qty']) {
                        throw new \Exception("Not enough stock for {$phone->name}");
                    }

                    $subtotal = $details['price'] * $details['qty'];
                    $totalAmount += $subtotal;
                    $itemsSummary .= "- {$phone->name} x{$details['qty']} ($".number_format($subtotal, 2).")\n";

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'phone_id' => $id,
                        'qty' => $details['qty'],
                        'price' => $details['price'],
                        'subtotal' => $subtotal,
                    ]);

                    $phone->decrement('qty', $details['qty']);
                }

                $sale->update([
                    'subtotal' => $totalAmount,
                    'total_price' => $totalAmount,
                ]);
            });

            // Send Telegram Notification
            $user = auth()->user();
            $message = "<b>💳 Visa Card Payment Confirmed!</b>\n\n";
            $message .= "<b>Bill No:</b> #{$billNumber}\n";
            $message .= "<b>Customer:</b> {$user->name}\n";
            $message .= "<b>Email:</b> {$user->email}\n";
            $message .= '<b>Total:</b> $'.number_format($totalAmount, 2)."\n\n";
            $message .= "<b>Items:</b>\n{$itemsSummary}";
            $message .= "<b>Status:</b> ✅ Payment Confirmed\n";
            $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

            // Use TelegramService
            $telegramService = app(\App\Services\TelegramService::class);
            $telegramService->sendMessage($message);

            session()->forget(['cart', 'pending_bill_no']);

            return redirect()->route('shop.success', $billNumber)->with('success', 'Payment confirmed! Thank you for your purchase.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function processABAPayment(Request $request, $billNumber, BakongService $bakongService)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        try {
            $totalAmount = 0;
            $itemsSummary = '';
            $userId = auth()->id();

            DB::transaction(function () use ($cart, $billNumber, $userId, &$totalAmount, &$itemsSummary) {
                $sale = Sale::create([
                    'bill_no' => $billNumber,
                    'user_id' => $userId,
                    'subtotal' => 0, // Placeholder
                    'tax' => 0,
                    'total_price' => 0, // Placeholder
                    'payment_method' => 'aba',
                    'status' => 'completed',
                ]);

                foreach ($cart as $id => $details) {
                    $phone = Phone::lockForUpdate()->findOrFail($id);

                    if ($phone->qty < $details['qty']) {
                        throw new \Exception("Not enough stock for {$phone->name}");
                    }

                    $subtotal = $details['price'] * $details['qty'];
                    $totalAmount += $subtotal;
                    $itemsSummary .= "- {$phone->name} x{$details['qty']} ($".number_format($subtotal, 2).")\n";

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'phone_id' => $id,
                        'qty' => $details['qty'],
                        'price' => $details['price'],
                        'subtotal' => $subtotal,
                    ]);

                    $phone->decrement('qty', $details['qty']);
                }

                $sale->update([
                    'subtotal' => $totalAmount,
                    'total_price' => $totalAmount,
                ]);
            });

            // Send Telegram Notification
            $user = auth()->user();
            $message = "<b>🏦 ABA Bank Payment Confirmed!</b>\n\n";
            $message .= "<b>Bill No:</b> #{$billNumber}\n";
            $message .= "<b>Customer:</b> {$user->name}\n";
            $message .= "<b>Email:</b> {$user->email}\n";
            $message .= '<b>Total:</b> $'.number_format($totalAmount, 2)."\n\n";
            $message .= "<b>Items:</b>\n{$itemsSummary}";
            $message .= "<b>Status:</b> ✅ Payment Confirmed\n";
            $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

            // Use TelegramService
            $telegramService = app(\App\Services\TelegramService::class);
            $telegramService->sendMessage($message);

            session()->forget(['cart', 'pending_bill_no']);

            return redirect()->route('shop.success', $billNumber)->with('success', 'Payment confirmed! Thank you for your purchase.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function processCashPayment($cart, $billNumber, $totalAmount)
    {
        if (empty($cart)) {
            return redirect()->route('shop.home')->with('error', 'Your cart is empty');
        }

        try {
            $itemsSummary = '';
            $userId = auth()->id();

            DB::transaction(function () use ($cart, $billNumber, $userId, $totalAmount, &$itemsSummary) {
                $sale = Sale::create([
                    'bill_no' => $billNumber,
                    'user_id' => $userId,
                    'subtotal' => $totalAmount,
                    'tax' => 0,
                    'total_price' => $totalAmount,
                    'payment_method' => 'cash',
                    'status' => 'pending', // Cash orders start as pending until payment received
                ]);

                foreach ($cart as $id => $details) {
                    $phone = Phone::lockForUpdate()->findOrFail($id);

                    if ($phone->qty < $details['qty']) {
                        throw new \Exception("Not enough stock for {$phone->name}");
                    }

                    $subtotal = $details['price'] * $details['qty'];
                    $itemsSummary .= "- {$phone->name} x{$details['qty']} ($".number_format($subtotal, 2).")\n";

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'phone_id' => $id,
                        'qty' => $details['qty'],
                        'price' => $details['price'],
                        'subtotal' => $subtotal,
                    ]);

                    // Reserve stock by decrementing quantity
                    $phone->decrement('qty', $details['qty']);
                }
            });

            // Send Telegram Notification
            $user = auth()->user();
            $message = "<b>💵 Cash Payment Order Received!</b>\n\n";
            $message .= "<b>Bill No:</b> #{$billNumber}\n";
            $message .= "<b>Customer:</b> {$user->name}\n";
            $message .= "<b>Email:</b> {$user->email}\n";
            $message .= '<b>Total:</b> $'.number_format($totalAmount, 2)."\n\n";
            $message .= "<b>Items:</b>\n{$itemsSummary}";
            $message .= "<b>Payment Method:</b> Cash on Delivery\n";
            $message .= "<b>Status:</b> ⏳ Pending Payment\n";
            $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

            // Use TelegramService
            $telegramService = app(\App\Services\TelegramService::class);
            $telegramService->sendMessage($message);

            session()->forget(['cart', 'pending_bill_no']);

            return redirect()->route('shop.success', $billNumber)->with('success', 'Order placed! Please prepare cash payment on delivery.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function success($billNo)
    {
        $sale = Sale::with(['items.phone', 'items.phone.category'])->where('bill_no', $billNo)->first();

        if (! $sale) {
            return redirect()->route('shop.home');
        }

        $totalAmount = $sale->total_price;
        $customer = auth()->user();

        return view('frontend.success', compact('sale', 'billNo', 'totalAmount', 'customer'));
    }

    public function trackOrder(Request $request)
    {
        $billNo = $request->get('bill_no');
        
        if (!$billNo) {
            return view('frontend.track-order');
        }

        $sale = Sale::with(['items.phone', 'user'])
            ->where('bill_no', $billNo)
            ->first();

        if (!$sale) {
            return redirect()->route('shop.track')->with('error', 'Order not found. Please check your order number.');
        }

        return view('frontend.track-order', compact('sale'));
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);
        $phones = Phone::where('category_id', $id)->get();
        $settings = \App\Helpers\SettingsHelper::getAllForView();

        return view('frontend.category', compact('category', 'phones', 'settings'));
    }

    public function product($id)
    {
        $phone = Phone::findOrFail($id);
        $settings = \App\Helpers\SettingsHelper::getAllForView();

        return view('frontend.product', compact('phone', 'settings'));
    }

    public function addToCart($id)
    {
        // Require authentication before adding to cart
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart.');
        }

        // Only customers can add to cart, not admins
        if (Auth::user()->role !== 'customer') {
            return redirect()->back()->with('error', 'Only customers can add items to cart.');
        }

        $phone = Phone::findOrFail($id);

        // Check if product has stock
        if ($phone->qty <= 0) {
            return redirect()->back()->with('error', 'This item is out of stock.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            // Check if adding one more would exceed available stock
            if ($cart[$id]['qty'] + 1 > $phone->qty) {
                return redirect()->back()->with('error', 'Cannot add more items. Only '.$phone->qty.' available in stock.');
            }
            $cart[$id]['qty']++;
        } else {
            $cart[$id] = [
                'name' => $phone->name,
                'price' => $phone->price,
                'image' => $phone->image,
                'qty' => 1,
                'stock' => $phone->qty,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Added to cart');
    }

    public function updateCart(Request $request)
    {
        // Require authentication
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login.');
        }

        // Only customers can update cart
        if (Auth::user()->role !== 'customer') {
            return redirect()->back()->with('error', 'Only customers can modify cart.');
        }

        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        // Validate quantity
        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        $phone = Phone::findOrFail($productId);

        // Check stock availability
        if ($quantity > $phone->qty) {
            return redirect()->back()->with('error', 'Only '.$phone->qty.' items available in stock.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] = $quantity;
            $cart[$productId]['stock'] = $phone->qty;
            session()->put('cart', $cart);

            return redirect()->back()->with('success', 'Cart updated successfully.');
        }

        return redirect()->back()->with('error', 'Product not found in cart.');
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product removed from cart');
    }

    public function cart()
    {
        $settings = \App\Helpers\SettingsHelper::getAllForView();
        return view('frontend.cart', compact('settings'));
    }

    /**
     * Handle Bakong Checkout return/callback
     */
    public function handleCheckoutReturn(Request $request, BakongService $bakongService)
    {
        $billNumber = $request->get('referenceId') ?? session('pending_bill_no');
        $checkoutToken = $request->get('token') ?? session('checkout_token');

        if (!$billNumber) {
            return redirect()->route('shop.home')->with('error', 'Invalid payment session');
        }

        // If we have a checkout token, verify payment status
        if ($checkoutToken) {
            $statusResult = $bakongService->checkCheckoutStatus($checkoutToken);

            if ($statusResult['success'] && $statusResult['paid']) {
                // Payment confirmed, process it
                return $this->processBakongPayment($billNumber, $bakongService);
            }
        }

        // If no token or payment not confirmed, check via original method
        return $this->processBakongPayment($billNumber, $bakongService);
    }
}

