<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'measured_at',
        'temperature',
        'humidity',
        'water_temperature',
        'ec',
        'ph',
        'light',
        'water_level',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'water_temperature' => 'decimal:2',
        'ec' => 'decimal:3',
        'ph' => 'decimal:2',
        'water_level' => 'decimal:2',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}