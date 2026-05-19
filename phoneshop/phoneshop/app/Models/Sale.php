<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    protected $fillable = [
        'bill_no', 
        'user_id', 
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_city',
        'customer_postal_code',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_status',
        'delivery_estimated_at',
        'delivery_completed_at',
        'delivery_driver_name',
        'delivery_driver_phone',
        'order_notes',
        'subtotal', 
        'tax', 
        'total_price', 
        'currency',
        'exchange_rate',
        'payment_method', 
        'payment_md5',
        'receipt_path',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason'
    ];

    protected $casts = [
        'delivery_estimated_at' => 'datetime',
        'delivery_completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
