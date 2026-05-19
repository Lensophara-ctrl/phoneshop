<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QRCodeController extends Controller
{
    /**
     * Generate PayWay Payment QR Code
     */
    public function generatePayWayQR(Request $request)
    {
        $request->validate([
            'tran_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        try {
            $size = $request->size ?? 300;
            $qrCode = QRCodeService::generatePayWayQRCode($request->tran_id, $request->amount);

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($qrCode, [
                    'payment_method' => 'payway',
                    'instructions' => [
                        '1. Open PayWay app or website',
                        '2. Select "Scan QR Code"',
                        '3. Scan the QR code above',
                        '4. Verify payment details',
                        '5. Confirm and complete payment',
                    ],
                ]),
                'message' => 'PayWay QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate Bank Transfer QR Code (KHQR)
     */
    public function generateBankTransferQR(Request $request)
    {
        $request->validate([
            'khqr' => 'required|string',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        try {
            $qrCode = QRCodeService::generateBankTransferQRCode($request->khqr);

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($qrCode, [
                    'payment_method' => 'bank_transfer',
                    'instructions' => [
                        '1. Open your bank app (ABA, ACLB, etc)',
                        '2. Select "Transfer by QR" or "Scan KHQR"',
                        '3. Point camera at the QR code above',
                        '4. Verify bank account and amount',
                        '5. Confirm and send the transfer',
                    ],
                ]),
                'message' => 'Bank transfer QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate Invoice QR Code
     */
    public function generateInvoiceQR(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'customer_email' => 'nullable|email',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        try {
            $qrCode = QRCodeService::generateInvoiceQRCode(
                $request->bill_number,
                $request->amount,
                $request->customer_email
            );

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'data' => $qrCode,
                'message' => 'Invoice QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate Contact Card QR Code (vCard)
     */
    public function generateContactQR(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'size' => 'nullable|integer|min:100|max:500',
        ]);

        try {
            $qrCode = QRCodeService::generateContactQRCode(
                $request->name,
                $request->email,
                $request->phone,
                $request->website
            );

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'data' => $qrCode,
                'message' => 'Contact card QR code generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate Complete Payment QR with Instructions
     */
    public function generatePaymentQR(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:payway,bank_transfer,khqr,2checkout',
            'khqr' => 'required_if:payment_method,khqr,bank_transfer',
        ]);

        try {
            if ($request->payment_method === 'bank_transfer' || $request->payment_method === 'khqr') {
                // For bank transfer, use the provided KHQR
                $qrCode = QRCodeService::generateBankTransferQRCode($request->khqr);
            } else {
                // For other methods
                $qrCode = QRCodeService::generatePayWayQRCode($request->transaction_id, $request->amount);
            }

            if (!$qrCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate QR code',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $paymentQR = QRCodeService::generatePaymentQRWithInstructions(
                $request->transaction_id,
                $request->amount,
                $request->payment_method
            );

            return response()->json([
                'success' => true,
                'data' => array_merge($paymentQR, ['qr_code' => $qrCode]),
                'message' => 'Payment QR code with instructions generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Test QR Code Generation
     */
    public function test()
    {
        try {
            $paymentQR = QRCodeService::generatePaymentQRWithInstructions(
                'TEST-001',
                100.00,
                'payway'
            );

            return response()->json([
                'success' => true,
                'data' => $paymentQR,
                'message' => 'QR code test successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
