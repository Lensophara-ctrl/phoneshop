<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\ABAMerchantService;
use App\Services\CambodianBankService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class SaleController extends Controller
{
    private $abaService;
    private $bankService;
    private $telegramService;

    public function __construct(ABAMerchantService $abaService, CambodianBankService $bankService, TelegramService $telegramService)
    {
        $this->abaService = $abaService;
        $this->bankService = $bankService;
        $this->telegramService = $telegramService;
    }

    public function index(Request $request)
    {
        $query = Sale::with(['items.phone', 'user'])
            ->where('status', 'completed'); // Only show completed sales in report

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter == 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($filter == 'this_month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            } elseif ($filter == 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->latest()->get();

        // Return JSON for AJAX requests (real-time polling)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sales' => $sales->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'bill_no' => $sale->bill_no,
                        'user' => $sale->user ? [
                            'name' => $sale->user->name,
                            'email' => $sale->user->email
                        ] : null,
                        'items_count' => $sale->items->count(),
                        'items_total_qty' => $sale->items->sum('qty'),
                        'total_price' => $sale->total_price,
                        'currency' => $sale->currency,
                        'payment_method' => $sale->payment_method,
                        'status' => $sale->status,
                        'created_at' => $sale->created_at->toIso8601String(),
                        'formatted_date' => $sale->created_at->format('M d, Y'),
                        'formatted_time' => $sale->created_at->format('h:i A'),
                    ];
                }),
                'summary' => [
                    'total_orders' => $sales->count(),
                    'total_revenue' => $sales->sum('total_price'),
                    'unique_customers' => $sales->unique('user_id')->count(),
                    'products_sold' => $sales->sum(fn($sale) => $sale->items->sum('qty'))
                ]
            ]);
        }

        return view('sales.index', compact('sales'));
    }

    /**
     * Get latest sales for real-time polling
     */
    public function latest(Request $request)
    {
        $query = Sale::with(['items.phone', 'user'])
            ->where('status', 'completed');

        // Get sales after the last sale ID (for polling)
        if ($request->filled('after_id')) {
            $query->where('id', '>', $request->after_id);
        }

        // Apply same filters as main index
        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter == 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($filter == 'this_month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            } elseif ($filter == 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $sales = $query->latest()->limit(50)->get();

        return response()->json([
            'success' => true,
            'sales' => $sales->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'bill_no' => $sale->bill_no,
                    'user' => $sale->user ? [
                        'name' => $sale->user->name,
                        'email' => $sale->user->email
                    ] : null,
                    'items_count' => $sale->items->count(),
                    'items_total_qty' => $sale->items->sum('qty'),
                    'total_price' => $sale->total_price,
                    'currency' => $sale->currency,
                    'payment_method' => $sale->payment_method,
                    'status' => $sale->status,
                    'created_at' => $sale->created_at->toIso8601String(),
                    'formatted_date' => $sale->created_at->format('M d, Y'),
                    'formatted_time' => $sale->created_at->format('h:i A'),
                ];
            }),
            'summary' => [
                'total_orders' => $sales->count(),
                'total_revenue' => $sales->sum('total_price'),
                'unique_customers' => $sales->unique('user_id')->count(),
                'products_sold' => $sales->sum(fn($sale) => $sale->items->sum('qty'))
            ]
        ]);
    }

    public function create()
    {
        $phones = Phone::with('category')->latest()->get();
        $categories = Category::all();
        $exchange_rate = 4100; // Can be fetched from settings if available

        return view('sales.create', compact('phones', 'categories', 'exchange_rate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.phone_id' => 'required|exists:phones,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'currency' => 'required|string|in:USD,KHR',
            'tax_amount' => 'nullable|numeric|min:0',
            'exchange_rate' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_city' => 'nullable|string|max:100',
            'customer_postal_code' => 'nullable|string|max:20',
            'order_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $saleItemsData = [];
            $phoneStockData = []; // Store original stock for rollback

            foreach ($request->items as $item) {
                $phone = Phone::lockForUpdate()->findOrFail($item['phone_id']);

                if ($item['qty'] > $phone->qty) {
                    throw new \Exception("Not enough stock for {$phone->name}!");
                }

                $itemSubtotal = $phone->price * $item['qty'];
                $subtotal += $itemSubtotal;

                $saleItemsData[] = [
                    'phone_id' => $phone->id,
                    'qty' => $item['qty'],
                    'price' => $phone->price,
                    'subtotal' => $itemSubtotal,
                ];

                // Store original stock
                $phoneStockData[$phone->id] = [
                    'phone' => $phone,
                    'original_qty' => $phone->qty,
                    'reduce_qty' => $item['qty']
                ];

                // Only reduce stock for CASH payments (immediate completion)
                // For Bakong/QR payments, stock will be reduced when payment is verified
                $isQrPayment = in_array($request->payment_method, ['aba_pay', 'bank_transfer', 'bakong']);
                if (!$isQrPayment) {
                    $phone->qty -= $item['qty'];
                    $phone->save();
                }
            }

            $tax = $request->tax_amount ?? 0;
            $total = $subtotal + $tax;
            $billNo = 'INV-' . strtoupper(uniqid());
            
            // If QR payment, set status to pending
            $isQrPayment = in_array($request->payment_method, ['aba_pay', 'bank_transfer', 'bakong']);
            $status = $isQrPayment ? 'pending' : 'completed';

            // Generate payment_md5 for Bakong verification
            $paymentMd5 = null;
            if ($request->payment_method === 'bakong') {
                $paymentMd5 = md5($billNo . $total . time());
            }

            $sale = Sale::create([
                'bill_no' => $billNo,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'customer_city' => $request->customer_city,
                'customer_postal_code' => $request->customer_postal_code,
                'order_notes' => $request->order_notes,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_price' => $total,
                'currency' => $request->currency,
                'exchange_rate' => $request->exchange_rate ?? 4100,
                'payment_method' => $request->payment_method,
                'status' => $status,
                'payment_md5' => $paymentMd5,
            ]);

            foreach ($saleItemsData as $itemData) {
                $itemData['sale_id'] = $sale->id;
                SaleItem::create($itemData);
            }

            DB::commit();

            // Send notifications for completed orders (cash payments)
            if ($status === 'completed') {
                try {
                    $sale->load(['items.phone', 'user']);
                    
                    // Send Telegram notification
                    try {
                        $items = $sale->items->map(function($item) {
                            return [
                                'name' => $item->phone->name ?? 'Unknown',
                                'qty' => $item->qty,
                                'price' => $item->price,
                                'subtotal' => $item->subtotal
                            ];
                        })->toArray();
                        
                        $this->telegramService->sendPaymentNotification([
                            'bill_no' => $sale->bill_no,
                            'amount' => $sale->total_price,
                            'currency' => $sale->currency,
                            'payment_method' => strtoupper($request->payment_method),
                            'status' => 'completed',
                            'customer_name' => $sale->user->name ?? 'Guest',
                            'items_count' => $sale->items->count(),
                            'items' => $items
                        ]);
                        
                        // Send invoice ready notification
                        $this->telegramService->sendInvoiceNotification(
                            $sale->bill_no,
                            $sale->customer_name ?? $sale->user->name ?? 'Guest',
                            $sale->customer_email ?? $sale->user->email ?? 'N/A',
                            $sale->total_price,
                            $sale->currency
                        );
                    } catch (\Exception $e) {
                        Log::error('Telegram notification error: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    Log::error('Notification error: ' . $e->getMessage());
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'sale_id' => $sale->id,
                    'bill_no' => $billNo,
                    'total' => $total,
                    'currency' => $request->currency,
                    'status' => $status,
                    'is_qr' => $isQrPayment
                ]);
            }

            return redirect()->route('sales.index')
                ->with('success', 'Sale completed successfully. Bill No: ' . $billNo);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function generateQR(Request $request, Sale $sale)
    {
        try {
            if ($sale->payment_method === 'aba_pay') {
                $qrCode = $this->abaService->generatePaymentQR($sale->bill_no, $sale->total_price);
                return response()->json([
                    'success' => true,
                    'qr_data' => $qrCode['data'] ?? null,
                    'qr_image' => $qrCode['image_uri'] ?? null,
                    'message' => 'ABA QR generated'
                ]);
            }
            
            if ($sale->payment_method === 'bakong') {
                // Use BakongService directly for real Bakong payment
                $bakongService = app(\App\Services\BakongService::class);
                $result = $bakongService->generateKHQR($sale->total_price, $sale->bill_no, $sale->currency);
                
                // Store payment_md5 for verification
                if (isset($result['data']['md5'])) {
                    $sale->update(['payment_md5' => $result['data']['md5']]);
                }
                
                return response()->json([
                    'success' => true,
                    'qr_data' => $result['data']['qr'] ?? null,
                    'qr_image' => $result['data']['qr_url'] ?? null,
                    'payment_md5' => $result['data']['md5'] ?? null,
                    'message' => 'Real Bakong KHQR generated'
                ]);
            }
            
            if ($sale->payment_method === 'bank_transfer') {
                $transferData = $this->bankService->createBankTransfer(
                    $sale->total_price, 
                    $sale->bill_no, 
                    auth()->user()->name,
                    null,
                    $sale->currency
                );
                
                return response()->json([
                    'success' => true,
                    'qr_data' => $transferData['khqr'],
                    'message' => 'Bank transfer KHQR generated'
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'QR not supported for this method'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStatus(Sale $sale)
    {
        try {
            if ($sale->status === 'completed') {
                return response()->json(['success' => true, 'status' => 'completed']);
            }

            if ($sale->payment_method === 'aba_pay') {
                $verification = $this->abaService->verifyPayment($sale->bill_no);
                if ($verification['verified']) {
                    DB::transaction(function () use ($sale) {
                        $sale->lockForUpdate();
                        if ($sale->status !== 'completed') {
                            $sale->update(['status' => 'completed']);
                            
                            // Reduce stock when payment is verified
                            foreach ($sale->items as $saleItem) {
                                $phone = Phone::lockForUpdate()->find($saleItem->phone_id);
                                if ($phone) {
                                    $phone->qty -= $saleItem->qty;
                                    $phone->save();
                                }
                            }
                        }
                    });
                    
                    // Send email to customer - DISABLED
                    // try {
                    //     $sale->load(['items.phone.category', 'user']);
                    //     Mail::to($sale->user->email)->send(new OrderConfirmation($sale));
                    // } catch (\Exception $e) {
                    //     Log::error('Failed to send order confirmation email', [
                    //         'sale_id' => $sale->id,
                    //         'error' => $e->getMessage()
                    //     ]);
                    // }
                    
                    // Send Telegram notification
                    try {
                        $sale->load(['items.phone', 'user']);
                        $items = $sale->items->map(function($item) {
                            return [
                                'name' => $item->phone->name ?? 'Unknown',
                                'qty' => $item->qty,
                                'price' => $item->price,
                                'subtotal' => $item->subtotal
                            ];
                        })->toArray();
                        
                        $this->telegramService->sendPaymentNotification([
                            'bill_no' => $sale->bill_no,
                            'amount' => $sale->total_price,
                            'currency' => $sale->currency,
                            'payment_method' => 'ABA PAY',
                            'status' => 'completed',
                            'customer_name' => $sale->user->name ?? 'Guest',
                            'items_count' => $sale->items->count(),
                            'items' => $items
                        ]);
                        
                        // Send invoice ready notification
                        $this->telegramService->sendInvoiceNotification(
                            $sale->bill_no,
                            $sale->customer_name ?? $sale->user->name ?? 'Guest',
                            $sale->customer_email ?? $sale->user->email ?? 'N/A',
                            $sale->total_price,
                            $sale->currency
                        );
                    } catch (\Exception $e) {
                        Log::error('Telegram notification error: ' . $e->getMessage());
                    }
                    
                    return response()->json(['success' => true, 'status' => 'completed']);
                }
            }

            if ($sale->payment_method === 'bakong') {
                // Use BakongService for real payment verification
                if (!$sale->payment_md5) {
                    return response()->json(['success' => true, 'status' => 'pending', 'message' => 'No payment MD5']);
                }
                
                try {
                    $bakongService = app(\App\Services\BakongService::class);
                    $verification = $bakongService->checkPayment($sale->payment_md5);
                    
                    // Check multiple success indicators
                    $isSuccess = false;
                    if (isset($verification['responseCode']) && $verification['responseCode'] === 0) {
                        $isSuccess = true;
                    }
                    if (isset($verification['data']['status'])) {
                        $status = strtoupper($verification['data']['status']);
                        if (in_array($status, ['SUCCESS', 'COMPLETED', 'PAID'])) {
                            $isSuccess = true;
                        }
                    }
                    
                    if ($isSuccess) {
                        DB::transaction(function () use ($sale, $verification) {
                            $sale->lockForUpdate();
                            if ($sale->status !== 'completed') {
                                $sale->update(['status' => 'completed']);
                                
                                // Reduce stock when payment is verified
                                foreach ($sale->items as $saleItem) {
                                    $phone = Phone::lockForUpdate()->find($saleItem->phone_id);
                                    if ($phone) {
                                        $phone->qty -= $saleItem->qty;
                                        $phone->save();
                                    }
                                }
                                
                                // Send email to customer - DISABLED
                                // try {
                                //     $sale->load(['items.phone.category', 'user']);
                                //     Mail::to($sale->user->email)->send(new OrderConfirmation($sale));
                                // } catch (\Exception $e) {
                                //     Log::error('Failed to send order confirmation email', [
                                //         'sale_id' => $sale->id,
                                //         'error' => $e->getMessage()
                                //     ]);
                                // }
                            }
                        });
                        
                        // Send Telegram notification
                        try {
                            $sale->load(['items.phone', 'user']);
                            $items = $sale->items->map(function($item) {
                                return [
                                    'name' => $item->phone->name ?? 'Unknown',
                                    'qty' => $item->qty,
                                    'price' => $item->price,
                                    'subtotal' => $item->subtotal
                                ];
                            })->toArray();
                            
                            $this->telegramService->sendPaymentNotification([
                                'bill_no' => $sale->bill_no,
                                'amount' => $sale->total_price,
                                'currency' => $sale->currency,
                                'payment_method' => 'Bakong',
                                'status' => 'completed',
                                'transaction_id' => $verification['data']['transactionId'] ?? null,
                                'customer_name' => $sale->user->name ?? 'Guest',
                                'items_count' => $sale->items->count(),
                                'items' => $items
                            ]);
                            
                            // Send invoice ready notification
                            $this->telegramService->sendInvoiceNotification(
                                $sale->bill_no,
                                $sale->customer_name ?? $sale->user->name ?? 'Guest',
                                $sale->customer_email ?? $sale->user->email ?? 'N/A',
                                $sale->total_price,
                                $sale->currency
                            );
                        } catch (\Exception $e) {
                            Log::error('Telegram notification error: ' . $e->getMessage());
                        }
                        
                        return response()->json([
                            'success' => true, 
                            'status' => 'completed',
                            'transaction_id' => $verification['data']['transactionId'] ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Bakong payment verification error', [
                        'sale_id' => $sale->id,
                        'payment_md5' => $sale->payment_md5,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($sale->payment_method === 'bank_transfer') {
                try {
                    $verification = $this->bankService->verifyBankTransfer($sale->bill_no);
                    if ($verification['verified'] && $verification['status'] === 'SUCCESS') {
                        DB::transaction(function () use ($sale) {
                            $sale->lockForUpdate();
                            if ($sale->status !== 'completed') {
                                $sale->update(['status' => 'completed']);
                                
                                // Reduce stock when payment is verified
                                foreach ($sale->items as $saleItem) {
                                    $phone = Phone::lockForUpdate()->find($saleItem->phone_id);
                                    if ($phone) {
                                        $phone->qty -= $saleItem->qty;
                                        $phone->save();
                                    }
                                }
                            }
                        });
                        
                        // Send email to customer
                        try {
                            $sale->load(['items.phone.category', 'user']);
                            Mail::to($sale->user->email)->send(new OrderConfirmation($sale));
                        } catch (\Exception $e) {
                            Log::error('Failed to send order confirmation email', [
                                'sale_id' => $sale->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        // Send Telegram notification
                        try {
                            $sale->load(['items.phone', 'user']);
                            $items = $sale->items->map(function($item) {
                                return [
                                    'name' => $item->phone->name ?? 'Unknown',
                                    'qty' => $item->qty,
                                    'price' => $item->price,
                                    'subtotal' => $item->subtotal
                                ];
                            })->toArray();
                            
                            $this->telegramService->sendPaymentNotification([
                                'bill_no' => $sale->bill_no,
                                'amount' => $sale->total_price,
                                'currency' => $sale->currency,
                                'payment_method' => 'Bank Transfer',
                                'status' => 'completed',
                                'customer_name' => $sale->user->name ?? 'Guest',
                                'items_count' => $sale->items->count(),
                                'items' => $items
                            ]);
                            
                            // Send invoice ready notification
                            $this->telegramService->sendInvoiceNotification(
                                $sale->bill_no,
                                $sale->customer_name ?? $sale->user->name ?? 'Guest',
                                $sale->customer_email ?? $sale->user->email ?? 'N/A',
                                $sale->total_price,
                                $sale->currency
                            );
                        } catch (\Exception $e) {
                            Log::error('Telegram notification error: ' . $e->getMessage());
                        }
                        
                        return response()->json(['success' => true, 'status' => 'completed']);
                    }
                } catch (\Exception $e) {
                    // Log error but continue polling
                }
            }

            return response()->json(['success' => true, 'status' => 'pending']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.phone', 'user']);
        return view('sales.show', compact('sale'));
    }
}
