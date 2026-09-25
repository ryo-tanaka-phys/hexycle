<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'status',
        'description',
    ];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
    public function sensorLogs()
{
    return $this->hasMany(SensorLog::class);
}
}