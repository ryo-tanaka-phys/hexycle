<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'variety',
        'image_path',
        'is_active',
    ];

    public function eventProducts()
    {
        return $this->hasMany(EventProduct::class);
    }
    public function cultivations()
{
    return $this->hasMany(Cultivation::class);
}
}