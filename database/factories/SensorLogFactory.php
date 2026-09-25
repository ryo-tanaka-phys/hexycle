<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\SensorLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SensorLog>
 */
class SensorLogFactory extends Factory
{
    protected $model = SensorLog::class;

    public function definition(): array
    {
        return [
            'device_id' => Device::factory(),
            'measured_at' => now(),

            'temperature' => fake()->randomFloat(2, 20, 30),
            'humidity' => fake()->randomFloat(2, 40, 80),
            'water_temperature' => fake()->randomFloat(2, 18, 26),
            'ec' => fake()->randomFloat(3, 0.8, 2.0),
            'ph' => fake()->randomFloat(2, 5.5, 7.0),
            'light' => fake()->numberBetween(1000, 20000),
            'water_level' => fake()->randomFloat(2, 20, 100),
        ];
    }
}