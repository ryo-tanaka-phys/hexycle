<?php

namespace App\Http\Controllers;

use App\Models\EventProgram;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    public function index(EventProgram $eventProgram)
    {
        $visits = $eventProgram->visits()
            ->with('user')
            ->latest('entered_at')
            ->paginate(30);

        $insideCount = $eventProgram->visits()
            ->where('status', 'inside')
            ->count();

        $remainingCapacity = $eventProgram->capacity !== null
            ? max($eventProgram->capacity - $insideCount, 0)
            : null;

        return view('admin.visits.index', compact(
            'eventProgram',
            'visits',
            'insideCount',
            'remainingCapacity'
        ));
    }

    public function store(Request $request, EventProgram $eventProgram)
    {
        $validated = $request->validate([
            'guest_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $insideCount = $eventProgram->visits()
            ->where('status', 'inside')
            ->count();

        if (
            $eventProgram->capacity !== null
            && $insideCount >= $eventProgram->capacity
        ) {
            return back()->withErrors([
                'capacity' => '現在、定員に達しています。',
            ]);
        }

        $eventProgram->visits()->create([
            'user_id' => null,
            'guest_name' => $validated['guest_name'] ?? null,
            'entered_at' => now(),
            'exited_at' => null,
            'status' => 'inside',
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
}