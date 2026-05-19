<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CambodianBankService
{
    private $bakongToken;
    private $bakongApiUrl;
    private $merchantBankId;
    private $supportedBanks;

    public function __construct()
    {
        $this->bakongToken = env('BAKONG_TOKEN');
        $this->bakongApiUrl = env('BAKONG_API_URL', 'https://api-bakong.nbc.gov.kh');
        $this->merchantBankId = env('CAMBODIA_BANK_ACCOUNT');
        $this->supportedBanks = array_filter(array_map('trim', explode(',', env('SUPPORTED_CAMBODIA_BANKS', 'ABA,ACLB'))));
    }

    /**
     * Get list of supported Cambodia banks
     */
    public function getSupportedBanks()
    {
        return [
            'ABA' => [
                'name' => 'Advanced Bank of Asia',
                'code' => 'ABA',
                'swift' => 'ABAAKHPP',
                'supported' => in_array('ABA', $this->supportedBanks),
            ],
            'ACLB' => [
                'name' => 'Asia Commercial Bank',
                'code' => 'ACLB',
                'swift' => 'ACLBKHPP',
                'supported' => in_array('ACLB', $this->supportedBanks),
            ],
            'CANADIA' => [
                'name' => 'Canadia Bank',
                'code' => 'CANADIA',
                'swift' => 'CBKHKHPP',
                'supported' => in_array('CANADIA', $this->supportedBanks),
            ],
            'SATHAPANA' => [
                'name' => 'Sathapana Bank',
                'code' => 'SATHAPANA',
                'swift' => 'SATHKHPP',
                'supported' => in_array('SATHAPANA', $this->supportedBanks),
            ],
            'WING' => [
                'name' => 'Wing Bank',
                'code' => 'WING',
                'swift' => 'WINGKHPP',
                'supported' => in_array('WING', $this->supportedBanks),
            ],
            'PPF' => [
                'name' => 'Phnom Penh Finance',
                'code' => 'PPF',
                'swift' => 'PPFHKHPP',
                'supported' => in_array('PPF', $this->supportedBanks),
            ],
        ];
    }

    /**
     * Create bank transfer using Bakong KHQR
     */
    public function createBankTransfer($amount, $billNumber, $customerName, $customerPhone = null, $currency = 'USD')
    {
        try {
            if (! env('CAMBODIA_BANK_ENABLED')) {
                throw new \Exception('Cambodia bank transfer is not enabled');
            }

            $bankCode = env('CAMBODIA_BANK_NAME', 'ACLB');
            $supportedBanks = $this->getSupportedBanks();

            if (! isset($supportedBanks[$bankCode])) {
                throw new \Exception("Bank code {$bankCode} is not supported");
            }

            if (! $supportedBanks[$bankCode]['supported']) {
                throw new \Exception("Bank {$bankCode} is not enabled for payments");
            }

            $bakongService = new BakongService();
            $khqrResult = $bakongService->generateKHQR($amount, $billNumber, $currency);
            
            // Extract QR string from result (now returns array)
            $khqr = is_array($khqrResult) && isset($khqrResult['data']['qr']) 
                ? $khqrResult['data']['qr'] 
                : $khqrResult;

            $transferData = [
                'bank_code' => $bankCode,
                'bank_name' => $supportedBanks[$bankCode]['name'],
                'bank_swift' => $supportedBanks[$bankCode]['swift'],
                'merchant_account' => env('CAMBODIA_BANK_ACCOUNT'),
                'merchant_account_name' => env('CAMBODIA_BANK_ACCOUNT_NAME'),
                'merchant_account_holder' => env('CAMBODIA_BANK_ACCOUNT_HOLDER'),
                'amount' => $amount,
                'currency' => $currency,
                'bill_number' => $billNumber,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'khqr' => $khqr,
                'status' => 'pending',
            ];

            Log::info('Cambodia bank transfer initiated', [
                'bill_number' => $billNumber,
                'amount' => $amount,
                'bank' => $bankCode,
            ]);

            return $transferData;
        } catch (\Exception $e) {
            Log::error('Cambodia bank transfer creation failed', [
                'error' => $e->getMessage(),
                'bill_number' => $billNumber,
            ]);
            throw $e;
        }
    }

    /**
     * Verify bank transfer status via Bakong
     */
    public function verifyBankTransfer($billNumber)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer '.$this->bakongToken,
                'Content-Type' => 'application/json',
            ];

            $payload = [
                'bill_number' => $billNumber,
            ];

            $response = Http::withHeaders($headers)->post(
                $this->bakongApiUrl.'/v1/payment/verify',
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Bank transfer verification successful', [
                    'bill_number' => $billNumber,
                    'status' => $data['status'] ?? 'unknown',
                ]);

                return [
                    'verified' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'data' => $data,
                ];
            }

            throw new \Exception('Failed to verify transfer: '.$response->body());
        } catch (\Exception $e) {
            Log::error('Bank transfer verification failed', [
                'error' => $e->getMessage(),
                'bill_number' => $billNumber,
            ]);
            throw $e;
        }
    }

    /**
     * Get bank account details
     */
    public function getMerchantBankDetails($bankCode = null)
    {
        $bankCode = $bankCode ?? env('CAMBODIA_BANK_NAME', 'ACLB');
        $supportedBanks = $this->getSupportedBanks();

        if (! isset($supportedBanks[$bankCode])) {
            throw new \Exception("Bank code {$bankCode} is not supported");
        }

        return [
            'bank' => $supportedBanks[$bankCode],
            'account_number' => env('CAMBODIA_BANK_ACCOUNT'),
            'account_name' => env('CAMBODIA_BANK_ACCOUNT_NAME'),
            'account_holder' => env('CAMBODIA_BANK_ACCOUNT_HOLDER'),
            'swift_code' => $supportedBanks[$bankCode]['swift'],
        ];
    }

    /**
     * Generate bank transfer instruction for customer
     */
    public function generateTransferInstruction($amount, $billNumber, $customerName)
    {
        try {
            $bankDetails = $this->getMerchantBankDetails();
            $bank = $bankDetails['bank'];

            $instruction = [
                'payment_method' => 'bank_transfer',
                'bank_code' => $bank['code'],
                'bank_name' => $bank['name'],
                'merchant_account' => $bankDetails['account_number'],
                'merchant_name' => $bankDetails['account_name'],
                'amount' => $amount,
                'reference_number' => $billNumber,
                'description' => "Payment for PhoneShop Order {$billNumber}",
                'swift_code' => $bank['swift'],
                'instruction_text' => "Transfer {$amount} KHR/USD to {$bank['name']} account {$bankDetails['account_number']} ({$bankDetails['account_holder']}) with reference {$billNumber}",
            ];

            return $instruction;
        } catch (\Exception $e) {
            Log::error('Failed to generate transfer instruction', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process bank transfer callback from Bakong
     */
    public function processBankTransferCallback($data)
    {
        try {
            Log::info('Bank transfer callback received', $data);

            $billNumber = $data['bill_number'] ?? $data['reference_number'] ?? null;
            $amount = $data['amount'] ?? null;
            $status = $data['status'] ?? 'unknown';
            $transactionId = $data['transaction_id'] ?? null;

            if (! $billNumber || ! $amount) {
                throw new \Exception('Invalid callback data');
            }

            return [
                'bill_number' => $billNumber,
                'amount' => $amount,
                'status' => $status,
                'transaction_id' => $transactionId,
                'processed' => $status === 'success' || $status === 'completed',
            ];
        } catch (\Exception $e) {
            Log::error('Bank transfer callback processing failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get transfer history for a bill
     */
    public function getTransferHistory($billNumber)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer '.$this->bakongToken,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->get(
                $this->bakongApiUrl.'/v1/payment/history',
                ['bill_number' => $billNumber]
            );

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to fetch transfer history');
        } catch (\Exception $e) {
            Log::error('Failed to get transfer history', [
                'error' => $e->getMessage(),
                'bill_number' => $billNumber,
            ]);
            throw $e;
        }
    }
}
