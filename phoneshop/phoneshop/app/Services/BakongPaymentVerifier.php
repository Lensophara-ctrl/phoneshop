<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Phone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BakongPaymentVerifier
{
    protected $bakongService;
    protected $telegramService;

    public function __construct(BakongService $bakongService, TelegramService $telegramService)
    {
        $this->bakongService = $bakongService;
        $this->telegramService = $telegramService;
    }

    /**
     * Verify and process Bakong payment
     */
    public function verifyAndProcess(string $billNo): array
    {
        $sale = Sale::with(['items.phone', 'user'])
            ->where('bill_no', $billNo)
            ->first();

        if (!$sale) {
            return [
                'success' => false,
                'paid' => false,
                'message' => 'Order not found'
            ];
        }

        // Already completed
        if ($sale->status === 'completed') {
            return [
                'success' => true,
                'paid' => true,
                'message' => 'Payment already confirmed',
                'order' => $this->getOrderData($sale)
            ];
        }

        // Try to verify payment
        $verified = $this->verifyPayment($sale);

        if ($verified) {
            // Process payment
            $this->processPayment($sale);
            
            return [
                'success' => true,
                'paid' => true,
                'message' => 'Payment confirmed successfully!',
                'order' => $this->getOrderData($sale)
            ];
        }

        return [
            'success' => true,
            'paid' => false,
            'message' => 'Payment not yet confirmed. Please complete payment with Bakong app.',
            'order' => $this->getOrderData($sale)
        ];
    }

    /**
     * Verify payment using multiple methods
     */
    protected function verifyPayment(Sale $sale): bool
    {
        // Method 1: Check via MD5 (KHQR)
        if ($sale->payment_md5) {
            if ($this->verifyViaMD5($sale->payment_md5)) {
                Log::info('Payment verified via MD5', ['bill_no' => $sale->bill_no]);
                return true;
            }
        }

        // Method 2: Check via checkout token
        if (session('checkout_token')) {
            if ($this->verifyViaCheckoutToken(session('checkout_token'))) {
                Log::info('Payment verified via checkout token', ['bill_no' => $sale->bill_no]);
                return true;
            }
        }

        // Method 3: Manual verification flag (for testing)
        if (session('force_payment_' . $sale->bill_no)) {
            Log::info('Payment verified via manual flag', ['bill_no' => $sale->bill_no]);
            session()->forget('force_payment_' . $sale->bill_no);
            return true;
        }

        return false;
    }

    /**
     * Verify via MD5 hash
     */
    protected function verifyViaMD5(string $md5): bool
    {
        try {
            $result = $this->bakongService->checkPayment($md5);
            
            // Check response code
            if (isset($result['responseCode']) && ($result['responseCode'] === 0 || $result['responseCode'] === '0')) {
                return true;
            }

            // Check status
            if (isset($result['data']['status'])) {
                $status = strtoupper($result['data']['status']);
                if (in_array($status, ['SUCCESS', 'COMPLETED', 'PAID'])) {
                    return true;
                }
            }

            // Check paid flag
            if (isset($result['data']['paid']) && $result['data']['paid'] === true) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('MD5 verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify via checkout token
     */
    protected function verifyViaCheckoutToken(string $token): bool
    {
        try {
            $result = $this->bakongService->checkCheckoutStatus($token);
            return $result['success'] && $result['paid'];
        } catch (\Exception $e) {
            Log::error('Checkout token verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process confirmed payment
     */
    protected function processPayment(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            // Lock and update sale
            $sale->lockForUpdate();
            
            if ($sale->status !== 'completed') {
                $sale->update(['status' => 'completed']);
                
                // Reduce stock
                foreach ($sale->items as $item) {
                    $phone = Phone::lockForUpdate()->find($item->phone_id);
                    if ($phone && $phone->qty >= $item->qty) {
                        $phone->decrement('qty', $item->qty);
                    }
                }
            }
        });

        // Send notifications
        $this->sendNotifications($sale);
    }

    /**
     * Send all notifications
     */
    protected function sendNotifications(Sale $sale): void
    {
        try {
            // Build item list
            $itemsList = '';
            foreach ($sale->items as $item) {
                $phoneName = $item->phone ? $item->phone->name : 'Unknown Item';
                $itemsList .= "- {$phoneName} x{$item->qty} = $" . number_format($item->subtotal, 2) . "\n";
            }

            // Telegram notification
            $message = "<b>✅ Payment Confirmed!</b>\n\n";
            $message .= "<b>Bill No:</b> #{$sale->bill_no}\n";
            $message .= "<b>Customer:</b> " . ($sale->user->name ?? 'Guest') . "\n";
            $message .= "<b>Email:</b> " . ($sale->user->email ?? 'N/A') . "\n";
            $message .= "<b>Total:</b> $" . number_format($sale->total_price, 2) . "\n";
            $message .= "<b>Payment Method:</b> " . ucfirst(str_replace('_', ' ', $sale->payment_method)) . "\n\n";
            $message .= "<b>Items:</b>\n{$itemsList}";
            $message .= "<b>Status:</b> ✅ Customer paid, prepare the order!\n";
            $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

            $this->telegramService->sendMessage($message);

            // Email notification
            if ($sale->user && $sale->user->email) {
                try {
                    \Mail::to($sale->user->email)->send(new \App\Mail\OrderConfirmation($sale));
                } catch (\Exception $e) {
                    Log::error('Email notification error: ' . $e->getMessage());
                }
            }

            Log::info('Notifications sent successfully', ['bill_no' => $sale->bill_no]);
        } catch (\Exception $e) {
            Log::error('Notification error: ' . $e->getMessage());
        }
    }

    /**
     * Get order data for response
     */
    protected function getOrderData(Sale $sale): array
    {
        return [
            'bill_no' => $sale->bill_no,
            'total' => $sale->total_price,
            'status' => $sale->status,
            'payment_method' => $sale->payment_method,
            'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Force mark payment as completed (for testing)
     */
    public function forceComplete(string $billNo): array
    {
        $sale = Sale::with(['items.phone', 'user'])
            ->where('bill_no', $billNo)
            ->first();

        if (!$sale) {
            return [
                'success' => false,
                'message' => 'Order not found'
            ];
        }

        if ($sale->status === 'completed') {
            return [
                'success' => true,
                'message' => 'Payment already completed'
            ];
        }

        $this->processPayment($sale);

        return [
            'success' => true,
            'message' => 'Payment marked as completed successfully',
            'order' => $this->getOrderData($sale)
        ];
    }
}
