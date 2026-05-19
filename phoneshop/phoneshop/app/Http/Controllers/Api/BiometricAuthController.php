<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BiometricAuthService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BiometricAuthController extends Controller
{
    protected $biometricService;

    public function __construct(BiometricAuthService $biometricService)
    {
        $this->biometricService = $biometricService;
    }

    /**
     * Check if user has biometric enabled
     */
    public function checkBiometric(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $hasDevices = $user->biometricTokens()->active()->exists();

        return response()->json([
            'success' => true,
            'biometric_enabled' => $hasDevices,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Generate challenge for biometric authentication
     */
    public function generateChallenge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->biometricService->generateChallenge($request->email);

        return response()->json($result);
    }

    /**
     * Verify biometric authentication
     */
    public function verifyBiometric(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'device_id' => 'required|string',
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->biometricService->verifySignature(
            $request->user_id,
            $request->device_id,
            $request->signature
        );

        if ($result['success']) {
            // Log the user in
            Auth::login($result['user']);
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $result['user'],
                'redirect' => $result['user']->role === 'admin' ? '/dashboard' : '/',
            ]);
        }

        return response()->json($result, 401);
    }

    /**
     * Generate QR code for device pairing
     */
    public function generatePairingQR(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $result = $this->biometricService->generatePairingQR($user);

        return response()->json($result);
    }

    /**
     * Complete device pairing from QR scan
     */
    public function completePairing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pairing_token' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'required|string',
            'public_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->biometricService->completePairing(
            $request->pairing_token,
            $request->device_id,
            $request->device_name,
            $request->public_key
        );

        return response()->json($result);
    }

    /**
     * Register device for biometric authentication
     */
    public function registerDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'device_name' => 'required|string',
            'public_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $result = $this->biometricService->registerDevice(
            $user,
            $request->device_id,
            $request->device_name,
            $request->public_key
        );

        return response()->json($result);
    }

    /**
     * Get user's registered devices
     */
    public function getDevices(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $result = $this->biometricService->getUserDevices($user);

        return response()->json($result);
    }

    /**
     * Revoke device access
     */
    public function revokeDevice(Request $request, $tokenId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $result = $this->biometricService->revokeDevice($user, $tokenId);

        return response()->json($result);
    }
}
