<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'location',
        'capacity',
        'status',
        'image_path',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'capacity' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function visits()
{
    return $this->hasMany(Visit::class);
}

public function admissionReservations()
{
    return $this->hasMany(AdmissionReservation::class);
}
}