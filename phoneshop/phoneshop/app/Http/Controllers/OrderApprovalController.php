<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Phone;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderApprovalController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Display pending orders for approval
     */
    public function index(Request $request)
    {
        $query = Sale::with(['items.phone', 'user'])
            ->whereIn('approval_status', ['pending', 'approved', 'rejected']);

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        } else {
            // Default to pending orders
            $query->where('approval_status', 'pending');
        }

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'bill_no' => $order->bill_no,
                        'customer_name' => $order->customer_name ?? $order->user->name ?? 'Guest',
                        'customer_email' => $order->customer_email ?? $order->user->email ?? 'N/A',
                        'total_price' => $order->total_price,
                        'currency' => $order->currency,
                        'payment_method' => $order->payment_method,
                        'status' => $order->status,
                        'approval_status' => $order->approval_status,
                        'receipt_url' => $order->receipt_path ? asset('storage/' . $order->receipt_path) : null,
                        'items_count' => $order->items->count(),
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                        'approved_at' => $order->approved_at ? $order->approved_at->format('M d, Y H:i') : null,
                        'rejection_reason' => $order->rejection_reason,
                    ];
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]);
        }

        return view('orders.approval', compact('orders'));
    }

    /**
     * Show order details for approval
     */
    public function show(Sale $order)
    {
        $order->load(['items.phone.category', 'user', 'approver']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'bill_no' => $order->bill_no,
                    'customer' => [
                        'name' => $order->customer_name ?? $order->user->name ?? 'Guest',
                        'email' => $order->customer_email ?? $order->user->email ?? 'N/A',
                        'phone' => $order->customer_phone ?? 'N/A',
                        'address' => $order->customer_address ?? 'N/A',
                        'city' => $order->customer_city ?? 'N/A',
                        'postal_code' => $order->customer_postal_code ?? 'N/A',
                    ],
                    'items' => $order->items->map(function($item) {
                        return [
                            'phone_name' => $item->phone->name ?? 'Unknown',
                            'category' => $item->phone->category->name ?? 'N/A',
                            'qty' => $item->qty,
                            'price' => $item->price,
                            'subtotal' => $item->subtotal,
                            'image' => $item->phone->image ? asset('storage/' . $item->phone->image) : null,
                        ];
                    }),
                    'subtotal' => $order->subtotal,
                    'tax' => $order->tax,
                    'total_price' => $order->total_price,
                    'currency' => $order->currency,
                    'payment_method' => $order->payment_method,
                    'status' => $order->status,
                    'approval_status' => $order->approval_status,
                    'receipt_url' => $order->receipt_path ? asset('storage/' . $order->receipt_path) : null,
                    'order_notes' => $order->order_notes,
                    'created_at' => $order->created_at->format('M d, Y H:i A'),
                    'approved_at' => $order->approved_at ? $order->approved_at->format('M d, Y H:i A') : null,
                    'approved_by' => $order->approver ? $order->approver->name : null,
                    'rejection_reason' => $order->rejection_reason,
                ]
            ]);
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Approve an order
     */
    public function approve(Request $request, Sale $order)
    {
        try {
            if ($order->approval_status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already approved'
                ], 400);
            }

            DB::transaction(function () use ($order, $request) {
                $order->update([
                    'approval_status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                // If payment is completed and order is approved, reduce stock
                if ($order->status === 'completed') {
                    foreach ($order->items as $item) {
                        $phone = Phone::lockForUpdate()->find($item->phone_id);
                        if ($phone && $phone->qty >= $item->qty) {
                            $phone->decrement('qty', $item->qty);
                        }
                    }
                }
            });

            // Send Telegram notification
            try {
                $order->load(['items.phone', 'user']);
                $items = $order->items->map(function($item) {
                    return [
                        'name' => $item->phone->name ?? 'Unknown',
                        'qty' => $item->qty,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal
                    ];
                })->toArray();

                $message = "<b>✅ Order Approved!</b>\n\n";
                $message .= "<b>Bill No:</b> #{$order->bill_no}\n";
                $message .= "<b>Customer:</b> " . ($order->customer_name ?? $order->user->name ?? 'Guest') . "\n";
                $message .= "<b>Total:</b> {$order->currency} " . number_format($order->total_price, 2) . "\n";
                $message .= "<b>Payment:</b> " . strtoupper($order->payment_method) . "\n";
                $message .= "<b>Approved by:</b> " . auth()->user()->name . "\n";
                $message .= "<b>Items:</b> " . $order->items->count() . "\n\n";
                
                foreach ($items as $item) {
                    $message .= "• {$item['name']} x{$item['qty']} = {$order->currency} " . number_format($item['subtotal'], 2) . "\n";
                }

                $this->telegramService->sendMessage($message);
            } catch (\Exception $e) {
                Log::error('Telegram notification error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order approved successfully',
                'order' => [
                    'id' => $order->id,
                    'approval_status' => $order->approval_status,
                    'approved_at' => $order->approved_at->format('M d, Y H:i A'),
                    'approved_by' => auth()->user()->name,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Order approval error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject an order
     */
    public function reject(Request $request, Sale $order)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            if ($order->approval_status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is already rejected'
                ], 400);
            }

            $order->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->reason,
            ]);

            // Send Telegram notification
            try {
                $order->load(['user']);
                $message = "<b>❌ Order Rejected</b>\n\n";
                $message .= "<b>Bill No:</b> #{$order->bill_no}\n";
                $message .= "<b>Customer:</b> " . ($order->customer_name ?? $order->user->name ?? 'Guest') . "\n";
                $message .= "<b>Total:</b> {$order->currency} " . number_format($order->total_price, 2) . "\n";
                $message .= "<b>Rejected by:</b> " . auth()->user()->name . "\n";
                $message .= "<b>Reason:</b> {$request->reason}";

                $this->telegramService->sendMessage($message);
            } catch (\Exception $e) {
                Log::error('Telegram notification error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Order rejected successfully',
                'order' => [
                    'id' => $order->id,
                    'approval_status' => $order->approval_status,
                    'approved_at' => $order->approved_at->format('M d, Y H:i A'),
                    'approved_by' => auth()->user()->name,
                    'rejection_reason' => $order->rejection_reason,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Order rejection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval statistics
     */
    public function stats()
    {
        $stats = [
            'pending' => Sale::where('approval_status', 'pending')->count(),
            'approved' => Sale::where('approval_status', 'approved')->count(),
            'rejected' => Sale::where('approval_status', 'rejected')->count(),
            'total' => Sale::count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
