<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
