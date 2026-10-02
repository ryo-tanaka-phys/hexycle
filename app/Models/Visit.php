<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_program_id',
        'user_id',
        'guest_name',
        'entered_at',
        'exited_at',
        'status',
        'check_in_code',
        'party_size',
        'admission_reservation_id',
    ];

    protected $casts = [
        'entered_at' => 'datetime',
        'exited_at' => 'datetime',
    ];

    public function eventProgram()
    {
        return $this->belongsTo(EventProgram::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admissionReservation()
{
    return $this->belongsTo(AdmissionReservation::class);
}
}
