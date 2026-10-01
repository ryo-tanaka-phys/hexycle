<?php

namespace App\Http\Controllers;

use App\Models\AdmissionReservation;
use App\Models\EventProgram;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmissionReservationController extends Controller
{
    public function create(EventProgram $eventProgram)
    {
        $slots = [];

        if ($eventProgram->start_at && $eventProgram->end_at) {
            $current = $eventProgram->start_at->copy();
            $end = $eventProgram->end_at->copy();

            while ($current < $end) {
                $slotStart = $current->copy();
                $slotEnd = $current->copy()->addMinutes(30);

                if ($slotEnd > $end) {
                    break;
                }

                $reservedCount = $eventProgram
                    ->admissionReservations()
                    ->where('slot_start', $slotStart)
                    ->whereIn('status', [
                        'reserved',
                        'checked_in',
                    ])
                    ->sum('party_size');

                $remaining = $eventProgram->capacity !== null
                    ? max(
                        $eventProgram->capacity - $reservedCount,
                        0
                    )
                    : null;

                $slots[] = [
                    'start' => $slotStart,
                    'end' => $slotEnd,
                    'reserved_count' => $reservedCount,
                    'remaining' => $remaining,
                ];

                $current->addMinutes(30);
            }
        }

        return view(
            'admission-reservations.create',
            compact('eventProgram', 'slots')
        );
    }

    public function store(
        Request $request,
        EventProgram $eventProgram
    ) {
        $validated = $request->validate([
            'guest_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'slot_start' => [
                'required',
                'date',
            ],
            'party_size' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
        ]);

        $slotStart = Carbon::parse(
            $validated['slot_start']
        );

        $slotEnd = $slotStart
            ->copy()
            ->addMinutes(30);

        // イベント開始時刻から30分刻みか確認
        $minutesFromStart = $eventProgram->start_at
            ->diffInMinutes($slotStart, false);

        if (
            $minutesFromStart < 0
            || $minutesFromStart % 30 !== 0
        ) {
            return back()
                ->withErrors([
                    'slot_start' => '無効な予約時間帯です。',
                ])
                ->withInput();
        }

        // 開催時間内か確認
        if (
            $slotStart < $eventProgram->start_at
            || $slotEnd > $eventProgram->end_at
        ) {
            return back()
                ->withErrors([
                    'slot_start' => '予約可能時間外です。',
                ])
                ->withInput();
        }

        // 同時予約による定員超過を防止
        $result = DB::transaction(function () use (
            $eventProgram,
            $validated,
            $slotStart,
            $slotEnd
        ) {
            $lockedProgram = EventProgram::whereKey(
                $eventProgram->id
            )
                ->lockForUpdate()
                ->firstOrFail();

            $reservedCount = $lockedProgram
                ->admissionReservations()
                ->where('slot_start', $slotStart)
                ->whereIn('status', [
                    'reserved',
                    'checked_in',
                ])
                ->sum('party_size');

            if (
                $lockedProgram->capacity !== null
                && $reservedCount + $validated['party_size']
                    > $lockedProgram->capacity
            ) {
                return false;
            }

            $reservation = AdmissionReservation::create([
    'event_program_id' => $lockedProgram->id,
    'user_id' => auth()->id(),
    'guest_name' => $validated['guest_name'] ?? null,
    'slot_start' => $slotStart,
    'slot_end' => $slotEnd,
    'party_size' => $validated['party_size'],
    'status' => 'reserved',
    'reservation_code' => (string) Str::uuid(),
]);

return $reservation;
        });

        if (! $result) {
            return back()
                ->withErrors([
                    'party_size' => 'この時間帯の残席数を超えています。',
                ])
                ->withInput();
        }

        return redirect()
    ->route(
        'admission-reservations.show',
        $result->reservation_code
    );
    }
   
public function show(string $reservationCode)
{
    $reservation = AdmissionReservation::with('eventProgram')
        ->where('reservation_code', $reservationCode)
        ->firstOrFail();

    return view(
        'admission-reservations.show',
        compact('reservation')
    );
}

public function cancel(string $reservationCode)
{
    $reservation = AdmissionReservation::where(
        'reservation_code',
        $reservationCode
    )->firstOrFail();

    if ($reservation->status !== 'reserved') {
        return back()->withErrors([
            'reservation' => 'この予約はキャンセルできません。',
        ]);
    }

    $reservation->update([
        'status' => 'cancelled',
    ]);

    return redirect()
        ->route(
            'admission-reservations.show',
            $reservation->reservation_code
        )
        ->with('success', '予約をキャンセルしました。');
}
}