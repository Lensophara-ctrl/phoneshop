<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    // Telegram file size limits in bytes
    const MAX_FILE_SIZE_FREE = 2147483648; // 2 GB
    const MAX_FILE_SIZE_PREMIUM = 4294967296; // 4 GB
    const MAX_PHOTO_SIZE = 10485760; // 10 MB
    const MAX_DOCUMENT_SIZE = 2147483648; // 2 GB (for free accounts)

    protected $botToken;
    protected $chatId;
    protected $apiUrl;
    protected $enabled;

    public function __construct()
    {
        // Read from .env ONLY
        $this->enabled = env('TELEGRAM_ENABLED', true);
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = env('TELEGRAM_CHAT_ID', '');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Send a basic text message
     */
    public function sendMessage(string $message, ?string $chatId = null): bool
    {
        // Check if Telegram is enabled
        if (!$this->enabled) {
            Log::info('Telegram: Notifications are disabled in settings');
            return false;
        }

        $targetChatId = $chatId ?? $this->chatId;

        if (! $this->botToken || ! $targetChatId) {
            Log::warning('Telegram: Bot token or chat ID not configured');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", [
                'chat_id' => $targetChatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Telegram sendMessage error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a formatted notification with title
     */
    public function sendNotification(string $title, string $message, ?string $chatId = null): bool
    {
        $fullMessage = "<b>🔔 {$title}</b>\n\n{$message}";
        return $this->sendMessage($fullMessage, $chatId);
    }

    /**
     * Send a payment alert notification
     */
    public function sendPaymentAlert(string $billNo, float $amount, string $status, ?string $paymentMethod = null): bool
    {
        $emoji = $status === 'completed' ? '✅' : '⚠️';
        $message = "<b>{$emoji} Payment {$status}</b>\n\n";
        $message .= "<b>Bill No:</b> {$billNo}\n";
        $message .= "<b>Amount:</b> $" . number_format($amount, 2) . "\n";

        if ($paymentMethod) {
            $message .= "<b>Method:</b> {$paymentMethod}\n";
        }

        $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Send a new order notification
     */
    public function sendNewOrderNotification(string $billNo, float $total, int $itemCount, string $customerName): bool
    {
        $message = "<b>🛒 New Order Received!</b>\n\n";
        $message .= "<b>Bill No:</b> {$billNo}\n";
        $message .= "<b>Customer:</b> {$customerName}\n";
        $message .= "<b>Items:</b> {$itemCount}\n";
        $message .= "<b>Total:</b> $" . number_format($total, 2) . "\n";
        $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Send a test message to verify configuration
     */
    public function sendTestMessage(): array
    {
        if (! $this->botToken || ! $this->chatId) {
            return [
                'success' => false,
                'message' => 'Telegram bot token or chat ID not configured',
            ];
        }

        try {
            // Check bot info first
            $botInfo = Http::get("{$this->apiUrl}/getMe");
            
            if (! $botInfo->successful()) {
                return [
                    'success' => false,
                    'message' => 'Invalid bot token',
                ];
            }

            $botName = $botInfo->json('result.first_name');

            // Send test message
            $message = "<b>✅ PhoneShop Connected!</b>\n\n";
            $message .= "Bot: {$botName}\n";
            $message .= "Time: " . now()->format('Y-m-d H:i:s');
            
            $sent = $this->sendMessage($message);

            return [
                'success' => $sent,
                'message' => $sent ? 'Test message sent successfully!' : 'Failed to send message',
                'bot_name' => $botName,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Telegram is properly configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->botToken) && ! empty($this->chatId);
    }

    /**
     * Send payment success notification with detailed information
     */
    public function sendPaymentSuccess(array $paymentData): bool
    {
        $amount = $paymentData['amount'] ?? 0;
        $currency = $paymentData['currency'] ?? 'USD';
        $billNumber = $paymentData['bill_number'] ?? 'N/A';
        $storeLabel = $paymentData['store_label'] ?? 'PhoneShop';
        $mobileNumber = $paymentData['mobile_number'] ?? 'N/A';
        $transactionId = $paymentData['transaction_id'] ?? 'N/A';

        $currencySymbol = $currency === 'KHR' ? '៛' : '$';
        $formattedAmount = $currency === 'KHR' 
            ? number_format($amount) 
            : number_format($amount, 2);

        $message = "<b>✅ Payment Received!</b>\n\n";
        $message .= "<b>📋 Bill No:</b> {$billNumber}\n";
        $message .= "<b>💰 Amount:</b> {$currencySymbol}{$formattedAmount} {$currency}\n";
        $message .= "<b>🏪 Store:</b> {$storeLabel}\n";
        $message .= "<b>📱 Mobile:</b> {$mobileNumber}\n";
        $message .= "<b>🔖 Transaction ID:</b> {$transactionId}\n";
        $message .= "<b>⏰ Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Send payment notification (for POS system)
     */
    public function sendPaymentNotification(array $data): bool
    {
        $billNo = $data['bill_no'] ?? 'N/A';
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'USD';
        $paymentMethod = $data['payment_method'] ?? 'Unknown';
        $status = $data['status'] ?? 'pending';
        $transactionId = $data['transaction_id'] ?? null;
        $customerName = $data['customer_name'] ?? 'Guest';
        $itemsCount = $data['items_count'] ?? 0;
        $items = $data['items'] ?? [];

        $emoji = $status === 'completed' ? '✅' : '⏳';
        $currencySymbol = $currency === 'KHR' ? '៛' : '$';
        $formattedAmount = $currency === 'KHR' 
            ? number_format($amount) 
            : number_format($amount, 2);

        $message = "<b>{$emoji} New POS Payment {$status}!</b>\n\n";
        $message .= "<b>📋 Bill No:</b> {$billNo}\n";
        $message .= "<b>👤 Customer:</b> {$customerName}\n";
        $message .= "<b>💰 Amount:</b> {$currencySymbol}{$formattedAmount} {$currency}\n";
        $message .= "<b>💳 Method:</b> {$paymentMethod}\n";
        
        if ($itemsCount > 0) {
            $message .= "<b>📦 Items:</b> {$itemsCount}\n";
        }
        
        // Add item details
        if (!empty($items)) {
            $message .= "\n<b>🛒 Order Items:</b>\n";
            foreach ($items as $index => $item) {
                $itemName = $item['name'] ?? 'Unknown';
                $itemQty = $item['qty'] ?? 0;
                $itemPrice = $item['price'] ?? 0;
                $itemSubtotal = $item['subtotal'] ?? 0;
                
                $formattedPrice = $currency === 'KHR' 
                    ? number_format($itemPrice) 
                    : number_format($itemPrice, 2);
                $formattedSubtotal = $currency === 'KHR' 
                    ? number_format($itemSubtotal) 
                    : number_format($itemSubtotal, 2);
                
                $message .= ($index + 1) . ". {$itemName}\n";
                $message .= "   Qty: {$itemQty} × {$currencySymbol}{$formattedPrice} = {$currencySymbol}{$formattedSubtotal}\n";
            }
        }
        
        if ($transactionId) {
            $message .= "\n<b>🔖 Transaction:</b> {$transactionId}\n";
        }
        
        $message .= "<b>⏰ Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Validate file size before sending to Telegram
     * 
     * @param string $filePath Path to the file
     * @param bool $isPremium Whether the account is Telegram Premium
     * @return array ['valid' => bool, 'size' => int, 'limit' => int, 'message' => string]
     */
    public function validateFileSize(string $filePath, bool $isPremium = false): array
    {
        if (!file_exists($filePath)) {
            return [
                'valid' => false,
                'size' => 0,
                'limit' => 0,
                'message' => 'File does not exist',
            ];
        }

        $fileSize = filesize($filePath);
        $limit = $isPremium ? self::MAX_FILE_SIZE_PREMIUM : self::MAX_FILE_SIZE_FREE;

        if ($fileSize > $limit) {
            $fileSizeFormatted = $this->formatBytes($fileSize);
            $limitFormatted = $this->formatBytes($limit);
            
            return [
                'valid' => false,
                'size' => $fileSize,
                'limit' => $limit,
                'message' => "File size ({$fileSizeFormatted}) exceeds Telegram limit ({$limitFormatted})",
            ];
        }

        return [
            'valid' => true,
            'size' => $fileSize,
            'limit' => $limit,
            'message' => 'File size is valid',
        ];
    }

    /**
     * Send a document file to Telegram with size validation
     * 
     * @param string $filePath Path to the file
     * @param string|null $caption Optional caption for the file
     * @param string|null $chatId Optional chat ID (uses default if not provided)
     * @param bool $isPremium Whether the account is Telegram Premium
     * @return array ['success' => bool, 'message' => string, 'validation' => array]
     */
    public function sendDocument(string $filePath, ?string $caption = null, ?string $chatId = null, bool $isPremium = false): array
    {
        // Check if Telegram is enabled
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Telegram notifications are disabled',
                'validation' => null,
            ];
        }

        // Validate configuration
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Telegram bot token or chat ID not configured',
                'validation' => null,
            ];
        }

        // Validate file size
        $validation = $this->validateFileSize($filePath, $isPremium);
        
        if (!$validation['valid']) {
            Log::warning("Telegram: {$validation['message']}");
            return [
                'success' => false,
                'message' => $validation['message'],
                'validation' => $validation,
            ];
        }

        $targetChatId = $chatId ?? $this->chatId;

        try {
            $response = Http::attach(
                'document',
                file_get_contents($filePath),
                basename($filePath)
            )->post("{$this->apiUrl}/sendDocument", [
                'chat_id' => $targetChatId,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Document sent successfully',
                    'validation' => $validation,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send document: ' . $response->body(),
                'validation' => $validation,
            ];
        } catch (\Exception $e) {
            Log::error("Telegram sendDocument error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'validation' => $validation,
            ];
        }
    }

    /**
     * Send a photo to Telegram with size validation
     * 
     * @param string $filePath Path to the photo
     * @param string|null $caption Optional caption for the photo
     * @param string|null $chatId Optional chat ID (uses default if not provided)
     * @return array ['success' => bool, 'message' => string, 'validation' => array]
     */
    public function sendPhoto(string $filePath, ?string $caption = null, ?string $chatId = null): array
    {
        // Check if Telegram is enabled
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Telegram notifications are disabled',
                'validation' => null,
            ];
        }

        // Validate configuration
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Telegram bot token or chat ID not configured',
                'validation' => null,
            ];
        }

        // Check file exists
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'File does not exist',
                'validation' => null,
            ];
        }

        $fileSize = filesize($filePath);
        
        // Validate photo size (10 MB limit)
        if ($fileSize > self::MAX_PHOTO_SIZE) {
            $fileSizeFormatted = $this->formatBytes($fileSize);
            $limitFormatted = $this->formatBytes(self::MAX_PHOTO_SIZE);
            
            return [
                'success' => false,
                'message' => "Photo size ({$fileSizeFormatted}) exceeds Telegram limit ({$limitFormatted})",
                'validation' => [
                    'valid' => false,
                    'size' => $fileSize,
                    'limit' => self::MAX_PHOTO_SIZE,
                ],
            ];
        }

        $targetChatId = $chatId ?? $this->chatId;

        try {
            $response = Http::attach(
                'photo',
                file_get_contents($filePath),
                basename($filePath)
            )->post("{$this->apiUrl}/sendPhoto", [
                'chat_id' => $targetChatId,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Photo sent successfully',
                    'validation' => [
                        'valid' => true,
                        'size' => $fileSize,
                        'limit' => self::MAX_PHOTO_SIZE,
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send photo: ' . $response->body(),
                'validation' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Telegram sendPhoto error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'validation' => null,
            ];
        }
    }

    /**
     * Send order status change notification
     */
    public function sendOrderStatusNotification(string $billNo, string $oldStatus, string $newStatus, string $customerName, ?string $reason = null): bool
    {
        $statusEmojis = [
            'pending' => '⏳',
            'completed' => '✅',
            'cancelled' => '❌',
            'refunded' => '💰',
            'processing' => '⚙️'
        ];
        
        $emoji = $statusEmojis[$newStatus] ?? '📋';
        $statusText = ucwords(str_replace('_', ' ', $newStatus));
        $oldStatusText = ucwords(str_replace('_', ' ', $oldStatus));
        
        $message = "<b>{$emoji} Order Status Changed</b>\n\n";
        $message .= "<b>Bill No:</b> {$billNo}\n";
        $message .= "<b>Customer:</b> {$customerName}\n";
        $message .= "<b>Old Status:</b> {$oldStatusText}\n";
        $message .= "<b>New Status:</b> {$statusText}\n";
        
        if ($reason) {
            $message .= "<b>Reason:</b> {$reason}\n";
        }
        
        $message .= "<b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Send invoice notification with download link
     */
    public function sendInvoiceNotification(string $billNo, string $customerName, string $customerEmail, float $total, string $currency): bool
    {
        $invoiceUrl = url("/customer/orders/{$billNo}/invoice");
        
        $currencySymbol = $currency === 'KHR' ? '៛' : '$';
        $formattedAmount = $currency === 'KHR' 
            ? number_format($total) 
            : number_format($total, 2);
        
        $message = "<b>🎉 Order Completed - Invoice Ready!</b>\n\n";
        $message .= "<b>📋 Bill No:</b> {$billNo}\n";
        $message .= "<b>👤 Customer:</b> {$customerName}\n";
        $message .= "<b>📧 Email:</b> {$customerEmail}\n";
        $message .= "<b>💰 Total:</b> {$currencySymbol}{$formattedAmount} {$currency}\n\n";
        $message .= "<b>📄 Invoice:</b> Ready to download\n";
        $message .= "<b>🔗 Link:</b> {$invoiceUrl}\n\n";
        $message .= "<b>⏰ Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Format bytes to human-readable format
     * 
     * @param int $bytes File size in bytes
     * @param int $precision Decimal precision
     * @return string Formatted file size
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get file size information
     * 
     * @param string $filePath Path to the file
     * @return array ['exists' => bool, 'size' => int, 'formatted' => string]
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [
                'exists' => false,
                'size' => 0,
                'formatted' => 'N/A',
            ];
        }

        $size = filesize($filePath);
        
        return [
            'exists' => true,
            'size' => $size,
            'formatted' => $this->formatBytes($size),
        ];
    }
}

