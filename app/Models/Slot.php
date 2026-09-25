<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'level',
        'position',
        'status',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
    public function cultivations()
{
    return $this->hasMany(Cultivation::class);
}
}