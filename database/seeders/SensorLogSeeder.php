<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\SensorLog;
use Illuminate\Database\Seeder;

class SensorLogSeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::where('serial_number', 'HX-0001')
            ->firstOrFail();

        for ($hoursAgo = 23; $hoursAgo >= 0; $hoursAgo--) {
            SensorLog::updateOrCreate(
                [
                    'device_id' => $device->id,
                    'measured_at' => now()
                        ->subHours($hoursAgo)
                        ->startOfHour(),
                ],
                [
                    'temperature' => fake()->randomFloat(2, 22, 28),
                    'humidity' => fake()->randomFloat(2, 50, 75),
                    'water_temperature' => fake()->randomFloat(2, 20, 25),
                    'ec' => fake()->randomFloat(3, 1.0, 1.8),
                    'ph' => fake()->randomFloat(2, 5.8, 6.8),
                    'light' => fake()->numberBetween(3000, 15000),
                    'water_level' => fake()->randomFloat(2, 40, 100),
                ]
            );
        }
    }
}