<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request OTP code
     */
    public function requestOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type' => 'sometimes|in:login,register,reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $type = $request->type ?? 'login';

        // Check if user exists for login type
        if ($type === 'login') {
            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
        }

        // Create and send OTP
        $result = $this->otpService->createOtp(
            $email,
            $type,
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'type' => 'sometimes|in:login,register,reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->otpService->verifyOtp(
            $request->email,
            $request->code,
            $request->type ?? 'login'
        );

        return response()->json($result);
    }

    /**
     * Resend OTP code
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'type' => 'sometimes|in:login,register,reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create and send new OTP
        $result = $this->otpService->createOtp(
            $request->email,
            $request->type ?? 'login',
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * Get OTP statistics (admin only)
     */
    public function stats(Request $request)
    {
        // Add admin middleware check if needed
        $stats = $this->otpService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
