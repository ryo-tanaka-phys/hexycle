<?php

namespace Tests\Feature;

use App\Models\AdmissionReservation;
use App\Models\EventProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionReservationTest extends TestCase
{
    use RefreshDatabase;

    

    public function test_valid_time_slot_can_be_reserved(): void
{
    $program = EventProgram::factory()->create([
        'start_at' => '2026-11-01 11:00:00',
        'end_at' => '2026-11-01 16:00:00',
        'capacity' => 30,
        'status' => 'published',
    ]);

    $response = $this->post(
        route('admission-reservations.store', $program),
        [
            'guest_name' => 'Test Guest',
            'slot_start' => '2026-11-01 11:30:00',
            'party_size' => 2,
        ]
    );

    $reservation = AdmissionReservation::where([
        'event_program_id' => $program->id,
        'guest_name' => 'Test Guest',
    ])->firstOrFail();

    $response->assertRedirect(
        route(
            'admission-reservations.show',
            $reservation->reservation_code
        )
    );

    $this->assertDatabaseHas('admission_reservations', [
        'event_program_id' => $program->id,
        'guest_name' => 'Test Guest',
        'party_size' => 2,
        'status' => 'reserved',
    ]);
}
public function test_invalid_time_slot_cannot_be_reserved(): void
{
    $program = EventProgram::factory()->create([
        'start_at' => '2026-11-01 11:00:00',
        'end_at' => '2026-11-01 16:00:00',
        'capacity' => 30,
        'status' => 'published',
    ]);

    $response = $this->post(
        route('admission-reservations.store', $program),
        [
            'guest_name' => 'Invalid Guest',
            'slot_start' => '2026-11-01 11:10:00',
            'party_size' => 1,
        ]
    );

    $response->assertSessionHasErrors('slot_start');

    $this->assertDatabaseMissing('admission_reservations', [
        'event_program_id' => $program->id,
        'guest_name' => 'Invalid Guest',
    ]);
}
    public function test_reservation_cannot_exceed_slot_capacity(): void
{
    $program = EventProgram::factory()->create([
        'start_at' => '2026-11-01 11:00:00',
        'end_at' => '2026-11-01 16:00:00',
        'capacity' => 3,
        'status' => 'published',
    ]);

    AdmissionReservation::factory()->create([
        'event_program_id' => $program->id,
        'slot_start' => '2026-11-01 11:00:00',
        'slot_end' => '2026-11-01 11:30:00',
        'party_size' => 2,
        'status' => 'reserved',
    ]);

    $response = $this->post(
        route('admission-reservations.store', $program),
        [
            'guest_name' => 'Capacity Test',
            'slot_start' => '2026-11-01 11:00:00',
            'party_size' => 2,
        ]
    );

    $response->assertSessionHasErrors('party_size');

    $this->assertDatabaseMissing('admission_reservations', [
        'event_program_id' => $program->id,
        'guest_name' => 'Capacity Test',
    ]);

    $this->assertSame(
        2,
        (int) $program->admissionReservations()
            ->where('slot_start', '2026-11-01 11:00:00')
            ->whereIn('status', ['reserved', 'checked_in'])
            ->sum('party_size')
    );
}
public function test_reserved_admission_can_be_cancelled(): void
{
    $program = EventProgram::factory()->create([
        'start_at' => '2026-11-01 11:00:00',
        'end_at' => '2026-11-01 16:00:00',
        'capacity' => 30,
        'status' => 'published',
    ]);

    $reservation = AdmissionReservation::factory()->create([
        'event_program_id' => $program->id,
        'slot_start' => '2026-11-01 11:30:00',
        'slot_end' => '2026-11-01 12:00:00',
        'party_size' => 3,
        'status' => 'reserved',
    ]);

    $response = $this->patch(
        route(
            'admission-reservations.cancel',
            $reservation->reservation_code
        )
    );

    $response->assertRedirect(
        route(
            'admission-reservations.show',
            $reservation->reservation_code
        )
    );

    $reservation->refresh();

    $this->assertSame(
        'cancelled',
        $reservation->status
    );
}
}