<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_product_id',
        'quantity',
        'unit_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function eventProduct()
    {
        return $this->belongsTo(EventProduct::class);
    }
    public function cultivations()
{
    return $this->hasMany(Cultivation::class);
}
}