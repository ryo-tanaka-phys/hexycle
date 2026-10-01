<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventProgram>
 */
class EventProgramFactory extends Factory
{
    protected $model = EventProgram::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(2),
            'location' => 'QSHOP会場',
            'capacity' => 30,
            'status' => 'published',
            'image_path' => null,
        ];
    }
}
