<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    protected $model = Slot::class;

    public function definition(): array
    {
        return [
            'device_id' => Device::factory(),
            'level' => 1,
            'position' => fake()->randomElement([
                'A', 'B', 'C', 'D', 'E', 'F',
            ]),
            'status' => 'available',
        ];
    }
}

