<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class HexycleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::updateOrCreate(
            ['name' => 'QSHOP文化祭 2026'],
            [
                'description' => 'Hexycleで育成した苗を紹介・販売する文化祭イベントです。',
                'start_at' => '2026-11-01 10:00:00',
                'end_at' => '2026-11-02 17:00:00',
                'reservation_start_at' => '2026-10-01 00:00:00',
                'reservation_end_at' => '2026-10-31 23:59:59',
                'status' => 'published',
            ]
        );

        $lettuce = Product::updateOrCreate(
            ['name' => 'サニーレタス'],
            [
                'description' => 'Hexycleで育成する葉物野菜です。',
                'variety' => 'サニーレタス',
                'image_path' => null,
                'is_active' => true,
            ]
        );

        $basil = Product::updateOrCreate(
            ['name' => 'バジル'],
            [
                'description' => '香りの良いハーブです。',
                'variety' => 'バジル',
                'image_path' => null,
                'is_active' => true,
            ]
        );

        $mint = Product::updateOrCreate(
            ['name' => 'ミント'],
            [
                'description' => '爽やかな香りのハーブです。',
                'variety' => 'ミント',
                'image_path' => null,
                'is_active' => true,
            ]
        );

        EventProduct::updateOrCreate(
            [
                'event_id' => $event->id,
                'product_id' => $lettuce->id,
            ],
            [
                'price' => 300,
                'stock' => 20,
                'reservation_limit' => 3,
                'is_available' => true,
            ]
        );

        EventProduct::updateOrCreate(
            [
                'event_id' => $event->id,
                'product_id' => $basil->id,
            ],
            [
                'price' => 250,
                'stock' => 15,
                'reservation_limit' => 3,
                'is_available' => true,
            ]
        );

        EventProduct::updateOrCreate(
            [
                'event_id' => $event->id,
                'product_id' => $mint->id,
            ],
            [
                'price' => 200,
                'stock' => 10,
                'reservation_limit' => 3,
                'is_available' => true,
            ]
        );
    }
}