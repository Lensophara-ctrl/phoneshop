<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Sale::with(['user', 'items'])
            ->whereNotNull('delivery_latitude')
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('delivery_status', $status);
        }

        $deliveries = $query->paginate(20);

        return view('deliveries.index', compact('deliveries', 'status'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.phone', 'user']);
        return view('deliveries.show', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,out_for_delivery,delivered',
            'delivery_driver_name' => 'nullable|string|max:255',
            'delivery_driver_phone' => 'nullable|string|max:20',
            'delivery_estimated_at' => 'nullable|date',
        ]);

        $oldStatus = $sale->delivery_status;
        $newStatus = $validated['delivery_status'];

        // If status is delivered, set completion time
        if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
            $validated['delivery_completed_at'] = now();
        }

        $sale->update($validated);

        // Send Telegram notification if status changed
        if ($oldStatus !== $newStatus) {
            try {
                $sale->load(['user']);
                
                $statusEmojis = [
                    'pending' => '⏳',
                    'processing' => '📦',
                    'shipped' => '🚚',
                    'out_for_delivery' => '🛵',
                    'delivered' => '✅'
                ];
                
                $emoji = $statusEmojis[$newStatus] ?? '📋';
                $statusText = ucwords(str_replace('_', ' ', $newStatus));
                
                $message = "<b>{$emoji} Delivery Status Updated</b>\n\n";
                $message .= "<b>Bill No:</b> #{$sale->bill_no}\n";
                $message .= "<b>Customer:</b> " . ($sale->customer_name ?? $sale->user->name ?? 'Guest') . "\n";
                $message .= "<b>Status:</b> {$statusText}\n";
                
                if ($sale->delivery_driver_name) {
                    $message .= "<b>Driver:</b> {$sale->delivery_driver_name}\n";
                }
                
                if ($sale->delivery_driver_phone) {
                    $message .= "<b>Driver Phone:</b> {$sale->delivery_driver_phone}\n";
                }
                
                if ($sale->delivery_estimated_at) {
                    $message .= "<b>Estimated:</b> " . $sale->delivery_estimated_at->format('M d, Y H:i') . "\n";
                }
                
                if ($newStatus === 'delivered' && $sale->delivery_completed_at) {
                    $message .= "<b>Completed:</b> " . $sale->delivery_completed_at->format('M d, Y H:i') . "\n";
                }
                
                $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');
                
                $this->telegramService->sendMessage($message);
            } catch (\Exception $e) {
                Log::error('Telegram notification error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Delivery status updated successfully');
    }

    public function map()
    {
        $deliveries = Sale::whereNotNull('delivery_latitude')
            ->whereNotNull('delivery_longitude')
            ->whereIn('delivery_status', ['processing', 'shipped', 'out_for_delivery'])
            ->with(['user'])
            ->get();

        return view('deliveries.map', compact('deliveries'));
    }
}
