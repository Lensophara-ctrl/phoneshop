<?php

namespace App\Services;

use App\Models\User;
use App\Models\BiometricToken;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BiometricAuthService
{
    /**
     * Register device for biometric authentication
     */
    public function registerDevice(User $user, string $deviceId, string $deviceName, string $publicKey): array
    {
        // Create or update biometric token
        $token = BiometricToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_id' => $deviceId,
            ],
            [
                'device_name' => $deviceName,
                'public_key' => $publicKey,
                'is_active' => true,
                'last_used_at' => Carbon::now(),
            ]
        );

        return [
            'success' => true,
            'message' => 'Device registered successfully',
            'token_id' => $token->id,
        ];
    }

    /**
     * Generate challenge for biometric authentication
     */
    public function generateChallenge(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        // Generate random challenge
        $challenge = Str::random(64);
        
        // Store challenge in session or cache
        cache()->put("biometric_challenge_{$user->id}", $challenge, 300); // 5 minutes

        return [
            'success' => true,
            'challenge' => $challenge,
            'user_id' => $user->id,
        ];
    }

    /**
     * Verify biometric signature
     */
    public function verifySignature(int $userId, string $deviceId, string $signature): array
    {
        $token = BiometricToken::where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->where('is_active', true)
            ->first();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Device not registered',
            ];
        }

        // Get stored challenge
        $challenge = cache()->get("biometric_challenge_{$userId}");

        if (!$challenge) {
            return [
                'success' => false,
                'message' => 'Challenge expired',
            ];
        }

        // In production, verify signature with public key
        // For now, we'll simulate verification
        $verified = $this->verifySignatureWithPublicKey($challenge, $signature, $token->public_key);

        if (!$verified) {
            return [
                'success' => false,
                'message' => 'Invalid signature',
            ];
        }

        // Update last used
        $token->update(['last_used_at' => Carbon::now()]);

        // Clear challenge
        cache()->forget("biometric_challenge_{$userId}");

        return [
            'success' => true,
            'message' => 'Biometric authentication successful',
            'user' => $token->user,
        ];
    }

    /**
     * Verify signature with public key (simplified)
     */
    protected function verifySignatureWithPublicKey(string $challenge, string $signature, string $publicKey): bool
    {
        // In production, use proper cryptographic verification
        // This is a simplified version
        return !empty($signature) && !empty($publicKey);
    }

    /**
     * Generate QR code for device pairing
     */
    public function generatePairingQR(User $user): array
    {
        $pairingToken = Str::random(32);
        
        // Store pairing token
        cache()->put("pairing_token_{$pairingToken}", $user->id, 600); // 10 minutes

        $qrData = [
            'type' => 'biometric_pairing',
            'token' => $pairingToken,
            'user_id' => $user->id,
            'email' => $user->email,
            'expires_at' => Carbon::now()->addMinutes(10)->toIso8601String(),
        ];

        return [
            'success' => true,
            'qr_data' => json_encode($qrData),
            'pairing_token' => $pairingToken,
        ];
    }

    /**
     * Complete pairing from QR scan
     */
    public function completePairing(string $pairingToken, string $deviceId, string $deviceName, string $publicKey): array
    {
        $userId = cache()->get("pairing_token_{$pairingToken}");

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Invalid or expired pairing token',
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        // Register device
        $result = $this->registerDevice($user, $deviceId, $deviceName, $publicKey);

        // Clear pairing token
        cache()->forget("pairing_token_{$pairingToken}");

        return $result;
    }

    /**
     * Get user's registered devices
     */
    public function getUserDevices(User $user): array
    {
        $devices = BiometricToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_used_at', 'desc')
            ->get();

        return [
            'success' => true,
            'devices' => $devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'device_name' => $device->device_name,
                    'last_used' => $device->last_used_at?->diffForHumans(),
                    'created_at' => $device->created_at->format('M d, Y'),
                ];
            }),
        ];
    }

    /**
     * Revoke device access
     */
    public function revokeDevice(User $user, int $tokenId): array
    {
        $token = BiometricToken::where('user_id', $user->id)
            ->where('id', $tokenId)
            ->first();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Device not found',
            ];
        }

        $token->update(['is_active' => false]);

        return [
            'success' => true,
            'message' => 'Device access revoked',
        ];
    }
}
