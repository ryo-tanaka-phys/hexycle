<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'event_id',
        'product_id',
        'price',
        'stock',
        'reservation_limit',
        'is_available',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}