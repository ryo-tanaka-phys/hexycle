<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'name' => 'Hexycle-' . fake()->unique()->numberBetween(1, 9999),
            'serial_number' => 'HX-' . fake()->unique()->numerify('####'),
            'status' => 'active',
            'description' => fake()->sentence(),
        ];
    }
}
