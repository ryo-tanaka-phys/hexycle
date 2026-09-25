<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cultivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'slot_id',
        'order_item_id',
        'status',
        'planted_at',
        'assigned_at',
        'harvested_at',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}