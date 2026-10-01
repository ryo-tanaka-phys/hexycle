<?php

namespace Database\Factories;

use App\Models\EventProgram;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'event_program_id' => EventProgram::factory(),
            'user_id' => null,
            'guest_name' => fake()->name(),
            'entered_at' => now(),
            'exited_at' => null,
            'status' => 'inside',
            'check_in_code' => fake()->unique()->uuid(),
        ];
    }
}