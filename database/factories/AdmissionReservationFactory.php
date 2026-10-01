<?php

namespace Database\Factories;

use App\Models\AdmissionReservation;
use App\Models\EventProgram;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AdmissionReservation>
 */
class AdmissionReservationFactory extends Factory
{
    protected $model = AdmissionReservation::class;

    public function definition(): array
    {
        $slotStart = now()->addDay()->startOfHour();

        return [
            'event_program_id' => EventProgram::factory(),
            'user_id' => null,
            'guest_name' => fake()->name(),
            'slot_start' => $slotStart,
            'slot_end' => $slotStart->copy()->addMinutes(30),
            'party_size' => 1,
            'status' => 'reserved',
            'reservation_code' => (string) Str::uuid(),
        ];
    }
}