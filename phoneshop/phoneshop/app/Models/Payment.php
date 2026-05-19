<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'bill_no',
        'amount',
        'payment_method',
        'gateway',
        'status',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'json',
        'amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->hasMany(Sale::class, 'bill_no', 'bill_no');
    }
}
