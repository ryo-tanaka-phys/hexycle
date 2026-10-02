<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_program_id',
        'user_id',
        'guest_name',
        'slot_start',
        'slot_end',
        'party_size',
        'status',
        'reservation_code',
    ];

    protected $casts = [
        'slot_start' => 'datetime',
        'slot_end' => 'datetime',
        'party_size' => 'integer',
    ];

    public function eventProgram()
    {
        return $this->belongsTo(EventProgram::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function visit()
{
    return $this->hasOne(Visit::class);
}
}