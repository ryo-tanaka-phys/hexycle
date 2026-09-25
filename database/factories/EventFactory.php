<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+1 month');

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_at' => $start,
            'end_at' => (clone $start)->modify('+1 day'),
            'reservation_start_at' => now(),
            'reservation_end_at' => (clone $start)->modify('-1 day'),
            'status' => 'published',
        ];
    }
}