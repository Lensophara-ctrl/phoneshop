<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerOrderController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Display customer's orders
     */
    public function index()
    {
        $orders = Sale::where('user_id', auth()->id())
            ->with(['items.phone'])
            ->withCount('items')
            ->latest()
            ->paginate(10);
        
        // Check if orders are locked
        $lockedOrders = DB::table('locked_reports')
            ->where('report_type', 'order')
            ->whereIn('user_id', $orders->pluck('id'))
            ->pluck('locked_at', 'user_id');
        
        foreach ($orders as $order) {
            $order->is_locked = isset($lockedOrders[$order->id]);
            $order->locked_at = $lockedOrders[$order->id] ?? null;
        }
        
        return view('profile.orders', compact('orders'));
    }
    
    /**
     * Lock an order report
     */
    public function lockOrder(Sale $order)
    {
        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        // Check if already locked
        $exists = DB::table('locked_reports')
            ->where('user_id', $order->id)
            ->where('report_type', 'order')
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This order report is already locked'
            ], 400);
        }
        
        // Only allow locking completed orders
        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed orders can be locked'
            ], 400);
        }
        
        try {
            DB::table('locked_reports')->insert([
                'user_id' => $order->id,
                'report_type' => 'order',
                'locked_by' => auth()->id(),
                'locked_at' => now(),
                'notes' => 'Customer locked order report',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Customer locked order report', [
                'order_id' => $order->id,
                'bill_no' => $order->bill_no,
                'customer_id' => auth()->id(),
                'customer_name' => auth()->user()->name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order report locked successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to lock order report', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock order report'
            ], 500);
        }
    }
    
    /**
     * Download order invoice
     */
    public function downloadInvoice(Sale $order)
    {
        // Verify ownership
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $order->load(['items.phone.category', 'user']);
        
        // Check if locked
        $isLocked = DB::table('locked_reports')
            ->where('user_id', $order->id)
            ->where('report_type', 'order')
            ->exists();
        
        $lockedInfo = null;
        if ($isLocked) {
            $lockedInfo = DB::table('locked_reports')
                ->where('user_id', $order->id)
                ->where('report_type', 'order')
                ->first();
        }
        
        // Send Telegram notification about invoice download
        try {
            $message = "<b>📥 Invoice Downloaded</b>\n\n";
            $message .= "<b>📋 Bill No:</b> {$order->bill_no}\n";
            $message .= "<b>👤 Customer:</b> " . ($order->customer_name ?? $order->user->name ?? 'Guest') . "\n";
            $message .= "<b>📧 Email:</b> " . ($order->customer_email ?? $order->user->email ?? 'N/A') . "\n";
            $message .= "<b>💰 Amount:</b> {$order->currency} " . number_format($order->total_price, 2) . "\n";
            $message .= "<b>🔒 Locked:</b> " . ($isLocked ? 'Yes' : 'No') . "\n";
            $message .= "<b>⏰ Downloaded:</b> " . now()->format('Y-m-d H:i:s');
            
            $this->telegramService->sendMessage($message);
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
        }
        
        // Generate HTML invoice
        $html = view('invoices.customer-order', compact('order', 'isLocked', 'lockedInfo'))->render();
        
        // Return as downloadable HTML
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="invoice-' . $order->bill_no . '.html"');
    }
}
