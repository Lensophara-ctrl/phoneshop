<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\Sale;
use App\Services\BakongService;
use App\Services\CambodianBankService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankTransferController extends Controller
{
    private $bankService;
    private $bakongService;

    public function __construct(CambodianBankService $bankService, BakongService $bakongService)
    {
        $this->bankService = $bankService;
        $this->bakongService = $bakongService;
    }

    /**
     * Get supported Cambodia banks
     */
    public function getSupportedBanks()
    {
        try {
            $banks = $this->bankService->getSupportedBanks();
            $supportedOnly = array_filter($banks, fn ($bank) => $bank['supported']);

            return response()->json([
                'success' => true,
                'data' => array_values($supportedOnly),
                'message' => 'Supported Cambodia banks retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get supported banks', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Initiate bank transfer payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string|unique:sales,bill_no',
            'amount' => 'required|numeric|min:0.01',
            'customer_name' => 'required|string',
            'customer_phone' => 'nullable|string',
            'bank_code' => 'nullable|string',
        ]);

        try {
            $user = auth()->user();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $transferData = $this->bankService->createBankTransfer(
                $request->amount,
                $request->bill_number,
                $request->customer_name,
                $request->customer_phone
            );

            return response()->json([
                'success' => true,
                'data' => $transferData,
                'message' => 'Bank transfer request created successfully',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Bank transfer initiation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get transfer instructions for customer
     */
    public function getInstructions(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'customer_name' => 'required|string',
        ]);

        try {
            $instruction = $this->bankService->generateTransferInstruction(
                $request->amount,
                $request->bill_number,
                $request->customer_name
            );

            return response()->json([
                'success' => true,
                'data' => $instruction,
                'message' => 'Transfer instructions generated',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate instructions', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get merchant bank details
     */
    public function getMerchantDetails()
    {
        try {
            $details = $this->bankService->getMerchantBankDetails();

            return response()->json([
                'success' => true,
                'data' => $details,
                'message' => 'Merchant bank details retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get merchant details', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify bank transfer completion
     */
    public function verify(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string',
        ]);

        try {
            $verification = $this->bankService->verifyBankTransfer($request->bill_number);

            if ($verification['verified'] && $verification['status'] === 'completed') {
                DB::transaction(function () use ($request, $verification) {
                    $payment = \App\Models\Payment::firstOrCreate(
                        ['transaction_id' => $verification['transaction_id'] ?? 'bank_'.$request->bill_number],
                        [
                            'bill_no' => $request->bill_number,
                            'amount' => 0,
                            'payment_method' => 'cambodia_bank_transfer',
                            'gateway' => 'bakong',
                            'status' => 'completed',
                            'response_data' => json_encode($verification),
                        ]
                    );

                    $sale = Sale::where('bill_no', $request->bill_number)->first();
                    if ($sale) {
                        $user = auth()->user();
                        $message = "<b>🏦 Cambodia Bank Transfer Confirmed!</b>\n\n";
                        $message .= "<b>Bill No:</b> #{$request->bill_number}\n";
                        if ($user) {
                            $message .= "<b>Customer:</b> {$user->name}\n";
                        }
                        $message .= '<b>Total:</b> $'.number_format($sale->total_price, 2)."\n\n";
                        $message .= "<b>Status:</b> ✅ Payment Completed\n";

                        $this->bakongService->sendTelegramNotification($message);
                    }
                });
            }

            return response()->json([
                'success' => $verification['verified'],
                'data' => $verification,
                'message' => $verification['verified'] ? 'Payment verified' : 'Payment not yet received',
            ]);
        } catch (\Exception $e) {
            Log::error('Payment verification failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle bank transfer webhook callback from Bakong
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Bank transfer webhook received', $request->all());

            $callbackData = $this->bankService->processBankTransferCallback($request->all());

            if ($callbackData['processed']) {
                DB::transaction(function () use ($callbackData) {
                    $transactionId = $callbackData['transaction_id'] ?? 'bank_'.$callbackData['bill_number'];
                    $billNumber = $callbackData['bill_number'];
                    $amount = $callbackData['amount'];

                    $payment = \App\Models\Payment::firstOrCreate(
                        ['transaction_id' => $transactionId],
                        [
                            'bill_no' => $billNumber,
                            'amount' => $amount,
                            'payment_method' => 'cambodia_bank_transfer',
                            'gateway' => 'bakong',
                            'status' => 'completed',
                            'response_data' => json_encode($callbackData),
                        ]
                    );

                    // Get sales and customer info
                    $sales = Sale::where('bill_no', $billNumber)->with('user', 'phone')->get();
                    
                    if (!$sales->isEmpty()) {
                        // Build notification message
                        $customer = $sales->first()->user;
                        $itemsSummary = '';
                        
                        foreach ($sales as $sale) {
                            $itemsSummary .= "• {$sale->phone->name} x{$sale->qty} - \${$sale->total_price}\n";
                        }

                        $message = "<b>🏦 Cambodia Bank Transfer Confirmed!</b>\n\n";
                        $message .= "<b>Bill No:</b> #{$billNumber}\n";
                        $message .= "<b>Transaction ID:</b> {$transactionId}\n";
                        if ($customer) {
                            $message .= "<b>Customer:</b> {$customer->name}\n";
                            $message .= "<b>Email:</b> {$customer->email}\n";
                        }
                        $message .= "<b>Bank:</b> ".env('CAMBODIA_BANK_NAME', 'ACLB')."\n";
                        $message .= '<b>Total:</b> $'.number_format($amount, 2)."\n";
                        $message .= "<b>Items:</b>\n{$itemsSummary}";
                        $message .= "<b>Status:</b> ✅ Payment Completed\n";
                        $message .= "<b>Time:</b> ".now()->format('Y-m-d H:i:s')."\n";

                        // Send notification
                        $this->bakongService->sendTelegramNotification($message);

                        Log::info('Bank transfer notification sent', [
                            'bill_no' => $billNumber,
                            'transaction_id' => $transactionId,
                            'amount' => $amount,
                        ]);
                    }

                    Log::info('Bank transfer recorded', [
                        'bill_number' => $billNumber,
                        'amount' => $amount,
                    ]);
                });
            }

            return response()->json([
                'success' => true,
                'data' => $callbackData,
                'message' => 'Webhook processed',
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get transfer history
     */
    public function getHistory(Request $request)
    {
        $request->validate([
            'bill_number' => 'required|string',
        ]);

        try {
            $history = $this->bankService->getTransferHistory($request->bill_number);

            return response()->json([
                'success' => true,
                'data' => $history,
                'message' => 'Transfer history retrieved',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get transfer history', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
