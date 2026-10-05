<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SensorLog;


class DeviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_index_is_displayed(): void
    {
        $user = User::factory()->create([
    'role' => 'admin',
]);

        $device = Device::factory()->create([
            'name' => 'Hexycle-01',
            'serial_number' => 'HX-0001',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('devices.index'));

        $response->assertStatus(200);
        $response->assertSee('Hexycle-01');
        $response->assertSee('HX-0001');
    }

    public function test_device_detail_is_displayed(): void
    {
        $user = User::factory()->create([
    'role' => 'admin',
]);

        $device = Device::factory()->create([
            'name' => 'Hexycle-01',
            'serial_number' => 'HX-0001',
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('devices.show', $device));

        $response->assertStatus(200);
        $response->assertSee('Hexycle-01');
        $response->assertSee('HX-0001');
    }

    public function test_device_detail_displays_twelve_slots(): void
    {
        $user = User::factory()->create([
    'role' => 'admin',
]);

        $device = Device::factory()->create();

        foreach ([1, 2] as $level) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $position) {
                Slot::factory()->create([
                    'device_id' => $device->id,
                    'level' => $level,
                    'position' => $position,
                    'status' => 'available',
                ]);
            }
        }

        $response = $this
            ->actingAs($user)
            ->get(route('devices.show', $device));

        $response->assertStatus(200);

        foreach ([1, 2] as $level) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $position) {
                $response->assertSee(
                    'Level ' . $level . ' / ' . $position
                );
            }
        }
    }

    public function test_slot_belongs_to_device(): void
    {
        $device = Device::factory()->create();

        $slot = Slot::factory()->create([
            'device_id' => $device->id,
            'level' => 1,
            'position' => 'A',
        ]);

        $this->assertTrue(
            $slot->device->is($device)
        );

        $this->assertTrue(
            $device->slots->contains($slot)
        );
    }
    public function test_customer_cannot_access_device_pages(): void
{
    $user = User::factory()->create([
        'role' => 'customer',
    ]);

    $device = Device::factory()->create();

    $this->actingAs($user)
        ->get(route('devices.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('devices.show', $device))
        ->assertForbidden();
}
public function test_device_detail_page_is_displayed_without_sensor_logs(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $device = Device::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->get(route('devices.show', $device));

    $response->assertStatus(200);

    $response->assertSee(
        'センサーログはまだありません。'
    );

    $response->assertSee(
        '直近24時間のセンサーログはありません。'
    );
}
public function test_device_detail_only_shows_sensor_logs_from_last_24_hours(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $device = Device::factory()->create();

    SensorLog::factory()->create([
        'device_id' => $device->id,
        'measured_at' => now()->subHours(2),
        'temperature' => 21.5,
    ]);

    SensorLog::factory()->create([
        'device_id' => $device->id,
        'measured_at' => now()->subHours(30),
        'temperature' => 99.9,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('devices.show', $device));

    $response->assertStatus(200);

    $response->assertSee('21.5');
    $response->assertDontSee('99.9');
}
public function test_device_detail_shows_latest_sensor_log_as_current_values(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $device = Device::factory()->create();

    SensorLog::factory()->create([
        'device_id' => $device->id,
        'measured_at' => now()->subHours(2),
        'temperature' => 20.0,
    ]);

    SensorLog::factory()->create([
        'device_id' => $device->id,
        'measured_at' => now()->subMinutes(10),
        'temperature' => 25.5,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('devices.show', $device));

    $response->assertStatus(200);

    $response->assertSee('25.5');
}
public function test_device_detail_is_displayed_when_sensor_values_are_null(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $device = Device::factory()->create();

    SensorLog::factory()->create([
        'device_id' => $device->id,
        'measured_at' => now(),
        'temperature' => null,
        'humidity' => null,
        'water_temperature' => null,
        'ec' => null,
        'ph' => null,
        'light' => null,
        'water_level' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('devices.show', $device));

    $response->assertStatus(200);
}
}