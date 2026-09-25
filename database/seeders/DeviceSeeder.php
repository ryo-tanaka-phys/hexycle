<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::updateOrCreate(
            ['serial_number' => 'HX-0001'],
            [
                'name' => 'Hexycle-01',
                'status' => 'active',
                'description' => '文化祭で使用するHexycle試作機',
            ]
        );

        foreach ([1, 2] as $level) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $position) {
                $device->slots()->updateOrCreate(
                    [
                        'level' => $level,
                        'position' => $position,
                    ],
                    [
                        'status' => 'available',
                    ]
                );
            }
        }
    }
}