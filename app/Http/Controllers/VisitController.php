<?php

namespace App\Http\Controllers;

use App\Models\EventProgram;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AdmissionReservation;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function index(EventProgram $eventProgram)
{
    $visits = $eventProgram->visits()
        ->with('user')
        ->latest('entered_at')
        ->paginate(30);

    $reservations = $eventProgram->admissionReservations()
        ->whereIn('status', ['reserved', 'checked_in'])
        ->orderBy('slot_start')
        ->get();

    $insideCount = $eventProgram->visits()
        ->where('status', 'inside')
        ->sum('party_size');

    $remainingCapacity = $eventProgram->capacity !== null
        ? max($eventProgram->capacity - $insideCount, 0)
        : null;

    return view('admin.visits.index', compact(
    'eventProgram',
    'visits',
    'reservations',
    'insideCount',
    'remainingCapacity'
));
}

    public function store(Request $request, EventProgram $eventProgram)
    {
        $validated = $request->validate([
    'guest_name' => ['nullable', 'string', 'max:255'],
    'party_size' => ['required', 'integer', 'min:1', 'max:10'],
]);

        $insideCount = $eventProgram->visits()
    ->where('status', 'inside')
    ->sum('party_size');

        if (
    $eventProgram->capacity !== null
    && $insideCount + $validated['party_size'] > $eventProgram->capacity
) {
    return back()->withErrors([
        'party_size' => '定員を超えるため入場できません。',
    ]);
}

        $eventProgram->visits()->create([
            'user_id' => null,
            'guest_name' => $validated['guest_name'] ?? null,
            'entered_at' => now(),
            'exited_at' => null,
            'status' => 'inside',
            'party_size' => $validated['party_size'],
            'check_in_code' => (string) Str::uuid(),
        ]);

        return redirect()
            ->route('admin.visits.index', $eventProgram)
            ->with('success', '入場を登録しました。');
    }

    public function exit(Visit $visit)
    {
        if ($visit->status !== 'inside') {
            return back()->withErrors([
                'visit' => 'この来場者はすでに退場済みです。',
            ]);
        }

        $visit->update([
            'status' => 'exited',
            'exited_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.visits.index',
                $visit->event_program_id
            )
            ->with('success', '退場を記録しました。');
    }
    public function checkInReservation(AdmissionReservation $admissionReservation)
{
    $result = DB::transaction(function () use ($admissionReservation) {
        $reservation = AdmissionReservation::whereKey($admissionReservation->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($reservation->status !== 'reserved') {
            return 'not_reserved';
        }

        $insideCount = $reservation->eventProgram
            ->visits()
            ->where('status', 'inside')
            ->sum('party_size');

        if (
            $reservation->eventProgram->capacity !== null
            && $insideCount + $reservation->party_size
                > $reservation->eventProgram->capacity
        ) {
            return 'capacity_exceeded';
        }

        $reservation->eventProgram->visits()->create([
            'admission_reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'guest_name' => $reservation->guest_name,
            'party_size' => $reservation->party_size,
            'entered_at' => now(),
            'status' => 'inside',
            'check_in_code' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $reservation->update([
            'status' => 'checked_in',
        ]);

        return 'success';
    });

    if ($result === 'not_reserved') {
        return back()->withErrors([
            'reservation' => 'この予約はチェックインできません。',
        ]);
    }

    if ($result === 'capacity_exceeded') {
        return back()->withErrors([
            'reservation' => '定員を超えるためチェックインできません。',
        ]);
    }

    return back()->with(
        'success',
        '予約からチェックインしました。'
    );
}
}