<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\SensorLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensor_log_belongs_to_device(): void
    {
        $device = Device::factory()->create();

        $log = SensorLog::factory()->create([
            'device_id' => $device->id,
        ]);

        $this->assertTrue(
            $log->device->is($device)
        );
    }

    public function test_device_has_sensor_logs(): void
    {
        $device = Device::factory()->create();

        SensorLog::factory()->count(3)->create([
            'device_id' => $device->id,
        ]);

        $this->assertCount(
            3,
            $device->sensorLogs
        );
    }

    public function test_device_detail_displays_latest_sensor_information(): void
    {
        $user = User::factory()->create([
    'role' => 'admin',
]);
        $device = Device::factory()->create();

        SensorLog::factory()->create([
            'device_id' => $device->id,
            'measured_at' => now(),
            'temperature' => 25.50,
            'humidity' => 60.25,
            'ph' => 6.20,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('devices.show', $device));

        $response->assertStatus(200);
        $response->assertSee('最新センサー情報');
        $response->assertSee('25.50');
        $response->assertSee('60.25');
        $response->assertSee('6.20');
    }

    public function test_device_detail_displays_recent_sensor_history(): void
    {
        $user = User::factory()->create([
    'role' => 'admin',
]);
        $device = Device::factory()->create();

        SensorLog::factory()->create([
            'device_id' => $device->id,
            'measured_at' => now()->subHours(3),
            'temperature' => 24.80,
        ]);

        SensorLog::factory()->create([
            'device_id' => $device->id,
            'measured_at' => now()->subHours(30),
            'temperature' => 99.99,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('devices.show', $device));

        $response->assertStatus(200);
        $response->assertSee('直近24時間のセンサーログ');

        $response->assertSee('24.80');

        $response->assertDontSee('99.99');
    }
}