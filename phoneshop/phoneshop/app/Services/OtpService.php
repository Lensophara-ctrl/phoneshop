<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected $telegramService;
    protected $validityMinutes = 5; // OTP valid for 5 minutes
    protected $maxAttempts = 3; // Max verification attempts

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Generate a 6-digit OTP code
     */
    protected function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create and send OTP
     */
    public function createOtp(string $identifier, string $type = 'login', ?string $ipAddress = null): array
    {
        // Invalidate previous OTPs for this identifier and type
        Otp::forIdentifier($identifier)
            ->ofType($type)
            ->valid()
            ->update(['verified' => true]);

        // Generate new OTP
        $code = $this->generateCode();
        $expiresAt = Carbon::now()->addMinutes($this->validityMinutes);

        $otp = Otp::create([
            'identifier' => $identifier,
            'code' => $code,
            'type' => $type,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
        ]);

        // Send OTP via available channels
        $sent = $this->sendOtp($identifier, $code, $type);

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'expires_in' => $this->validityMinutes,
            'sent_via' => $sent,
        ];
    }

    /**
     * Send OTP via Telegram and/or Email
     */
    protected function sendOtp(string $identifier, string $code, string $type): array
    {
        $sentVia = [];
        $user = User::where('email', $identifier)->first();

        // Prepare message
        $typeLabel = ucfirst(str_replace('_', ' ', $type));
        $message = "🔐 Your {$typeLabel} OTP Code\n\n";
        $message .= "Code: <b>{$code}</b>\n\n";
        $message .= "⏰ Valid for {$this->validityMinutes} minutes\n";
        $message .= "⚠️ Do not share this code with anyone!";

        // Send via Telegram if configured
        if ($this->telegramService->isConfigured()) {
            try {
                $telegramSent = $this->telegramService->sendMessage($message);
                if ($telegramSent) {
                    $sentVia[] = 'telegram';
                }
            } catch (\Exception $e) {
                Log::error("Failed to send OTP via Telegram: " . $e->getMessage());
            }
        }

        // Send via Email if user exists
        if ($user) {
            try {
                \Mail::to($user->email)->send(new \App\Mail\OtpMail($code, $type, $this->validityMinutes));
                $sentVia[] = 'email';
            } catch (\Exception $e) {
                Log::error("Failed to send OTP via Email: " . $e->getMessage());
            }
        }

        return $sentVia;
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $identifier, string $code, string $type = 'login'): array
    {
        $otp = Otp::forIdentifier($identifier)
            ->ofType($type)
            ->where('code', $code)
            ->valid()
            ->latest()
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP code',
            ];
        }

        // Mark as verified
        $otp->markAsVerified();

        return [
            'success' => true,
            'message' => 'OTP verified successfully',
        ];
    }

    /**
     * Check if OTP is required for user
     */
    public function isOtpRequired(User $user): bool
    {
        // You can add logic here to determine if OTP is required
        // For example, based on user settings, role, or security level
        return env('OTP_ENABLED', true);
    }

    /**
     * Clean up expired OTPs (can be run via scheduled task)
     */
    public function cleanupExpiredOtps(): int
    {
        return Otp::where('expires_at', '<', Carbon::now()->subHours(24))
            ->delete();
    }

    /**
     * Get OTP statistics
     */
    public function getStats(): array
    {
        return [
            'total' => Otp::count(),
            'verified' => Otp::where('verified', true)->count(),
            'expired' => Otp::where('expires_at', '<', Carbon::now())
                ->where('verified', false)
                ->count(),
            'active' => Otp::valid()->count(),
        ];
    }
}
