<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    protected $fillable = ['name', 'image'];
}
