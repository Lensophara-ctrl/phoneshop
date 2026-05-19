<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    //
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $fillable = [
        'name',
        'category_id',
        'price',
        'qty',
        'image',
        'detail_images',
        'description',
    ];

    protected $casts = [
        'detail_images' => 'array',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
