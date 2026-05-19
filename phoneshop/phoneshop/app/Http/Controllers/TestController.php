<?php

namespace App\Http\Controllers;

use App\Services\BakongService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testTelegramNotification()
    {
        try {
            $bakongService = new BakongService();
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if (! $botToken || ! $chatId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram bot token or chat ID not configured',
                    'token_set' => !! $botToken,
                    'chat_id_set' => !! $chatId,
                ]);
            }

            $message = "<b>✅ Test Notification from PhoneShop</b>\n\n";
            $message .= "<b>Time:</b> ".now()->format('Y-m-d H:i:s')."\n";
            $message .= "<b>Server:</b> ".request()->getHttpHost()."\n\n";
            $message .= "If you see this, the notification system is working! 🎉";

            $bakongService->sendTelegramNotification($message);

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent! Check your Telegram.',
                'bot_token_first_10' => substr($botToken, 0, 10).'...',
                'chat_id' => $chatId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function testPaymentEndpoint()
    {
        $endpoints = [
            'Phones API' => [
                'GET /api/phones' => 'List all phones',
                'GET /api/phones/{id}' => 'Get single phone',
            ],
            'Bank Transfer' => [
                'GET /api/bank-transfer/supported-banks' => 'List Cambodia banks',
                'GET /api/bank-transfer/merchant-details' => 'Get merchant info',
                'POST /api/bank-transfer/instructions' => 'Get transfer instructions',
            ],
            '2Checkout Payment' => [
                'POST /api/payment/twocheckout/initiate' => 'Create payment session',
                'POST /api/payment/twocheckout/verify' => 'Verify payment',
                'POST /api/payment/twocheckout/refund' => 'Process refund',
            ],
            'Database Status' => [
                'Payments' => \App\Models\Payment::count().' records',
                'Sales' => \App\Models\Sale::count().' records',
                'Users' => \App\Models\User::count().' records',
            ],
        ];

        return response()->json([
            'success' => true,
            'server_status' => 'Running ✅',
            'timestamp' => now(),
            'endpoints' => $endpoints,
            'telegram_configured' => !! env('TELEGRAM_BOT_TOKEN'),
        ]);
    }

    public function testDatabaseStatus()
    {
        try {
            $tables = [
                'payments' => \App\Models\Payment::count(),
                'sales' => \App\Models\Sale::count(),
                'users' => \App\Models\User::count(),
                'phones' => \App\Models\Phone::count(),
                'categories' => \App\Models\Category::count(),
            ];

            $latestPayment = \App\Models\Payment::latest()->first();
            $latestSale = \App\Models\Sale::latest()->first();

            return response()->json([
                'success' => true,
                'database_status' => 'Connected ✅',
                'table_counts' => $tables,
                'latest_payment' => $latestPayment ? [
                    'id' => $latestPayment->id,
                    'transaction_id' => $latestPayment->transaction_id,
                    'amount' => $latestPayment->amount,
                    'status' => $latestPayment->status,
                    'created_at' => $latestPayment->created_at,
                ] : 'No payments yet',
                'latest_sale' => $latestSale ? [
                    'id' => $latestSale->id,
                    'bill_no' => $latestSale->bill_no,
                    'total_price' => $latestSale->total_price,
                    'created_at' => $latestSale->created_at,
                ] : 'No sales yet',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function testPhoneImages()
    {
        $phones = \App\Models\Phone::select('id', 'name', 'image')->limit(10)->get();
        
        return response()->json([
            'success' => true,
            'phones' => $phones->map(function($phone) {
                return [
                    'id' => $phone->id,
                    'name' => $phone->name,
                    'image' => $phone->image,
                    'image_url' => $phone->image ? asset('storage/'.$phone->image) : null,
                    'file_exists' => $phone->image ? file_exists(storage_path('app/public/'.$phone->image)) : false,
                ];
            }),
        ]);
    }
}
