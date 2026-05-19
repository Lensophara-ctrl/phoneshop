<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function uploadABAQRCode(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Delete old ABA QR code if exists
            $oldFiles = Storage::disk('public')->files('qrcodes');
            foreach ($oldFiles as $oldFile) {
                if (str_starts_with(basename($oldFile), 'aba-qr-code')) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            // Store new QR code using Laravel Storage
            $extension = $file->getClientOriginalExtension();
            $filename = 'aba-qr-code.' . $extension;
            $path = $file->storeAs('qrcodes', $filename, 'public');

            Log::info('ABA QR code uploaded', [
                'filename' => $filename,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ABA QR code uploaded successfully',
                'data' => [
                    'filename' => $filename,
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                ],
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('ABA QR code upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function uploadReceipt(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp,pdf|max:10240',
                'bill_no' => 'required|string|exists:sales,bill_no',
            ]);

            // Find the sale
            $sale = \App\Models\Sale::where('bill_no', $request->bill_no)->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file upload',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Delete old receipt if exists
            if ($sale->receipt_path && Storage::disk('public')->exists($sale->receipt_path)) {
                Storage::disk('public')->delete($sale->receipt_path);
            }

            // Store new receipt
            $extension = $file->getClientOriginalExtension();
            $filename = 'receipt-' . $request->bill_no . '-' . time() . '.' . $extension;
            $path = $file->storeAs('receipts', $filename, 'public');

            // Update sale with receipt path
            $sale->update(['receipt_path' => $path]);

            Log::info('Receipt uploaded', [
                'bill_no' => $request->bill_no,
                'filename' => $filename,
                'path' => $path,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Receipt uploaded successfully',
                'data' => [
                    'bill_no' => $request->bill_no,
                    'filename' => $filename,
                    'path' => $path,
                    'url' => asset('storage/' . $path),
                ],
            ], Response::HTTP_OK);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            Log::error('Receipt upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
