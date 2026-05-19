<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class PaymentNotificationController extends Controller
{
    /**
     * Check for new completed payments since timestamp
     */
    public function checkNewPayments(Request $request)
    {
        $since = $request->input('since');
        
        if (!$since) {
            return response()->json(['new_payments' => []]);
        }
        
        $sinceDate = date('Y-m-d H:i:s', $since / 1000);
        
        $newPayments = Sale::where('status', 'completed')
            ->where('updated_at', '>', $sinceDate)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($sale) {
                return [
                    'bill_no' => $sale->bill_no,
                    'amount' => $sale->total_price,
                    'updated_at' => $sale->updated_at->timestamp * 1000
                ];
            });
        
        return response()->json([
            'new_payments' => $newPayments
        ]);
    }
}
