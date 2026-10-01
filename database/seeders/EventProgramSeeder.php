<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventProgram;
use Illuminate\Database\Seeder;

class EventProgramSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::where('name', 'QSHOP文化祭 2026')
            ->firstOrFail();

        EventProgram::updateOrCreate(
            [
                'event_id' => $event->id,
                'title' => 'Hexycleイベントカフェ',
            ],
            [
                'description' => 'Hexycleの展示とあわせて楽しめる文化祭限定のイベントカフェです。',
                'start_at' => '2026-11-01 11:00:00',
                'end_at' => '2026-11-01 16:00:00',
                'location' => 'QSHOP会場',
                'capacity' => 30,
                'status' => 'published',
                'image_path' => null,
            ]
        );
    }
}