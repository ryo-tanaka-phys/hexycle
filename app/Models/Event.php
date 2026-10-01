<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_at',
        'end_at',
        'reservation_start_at',
        'reservation_end_at',
        'status',
    ];

    public function eventProducts()
    {
        return $this->hasMany(EventProduct::class);
    }
    public function orders()
{
    return $this->hasMany(Order::class);
}
public function programs()
{
    return $this->hasMany(EventProgram::class);
}
}