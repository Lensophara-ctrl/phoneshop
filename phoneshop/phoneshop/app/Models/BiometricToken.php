<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'public_key',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the token
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
