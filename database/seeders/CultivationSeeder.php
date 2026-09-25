<?php

namespace Database\Seeders;

use App\Models\Cultivation;
use App\Models\Device;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CultivationSeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::where('serial_number', 'HX-0001')
            ->with('slots')
            ->firstOrFail();

        $products = Product::orderBy('id')->get();

        if ($products->isEmpty()) {
            return;
        }

        $latestOrderItem = OrderItem::latest()->first();

        foreach ($device->slots as $index => $slot) {
            $product = $products[$index % $products->count()];

            Cultivation::updateOrCreate(
                [
                    'slot_id' => $slot->id,
                ],
                [
                    'product_id' => $product->id,
                    'order_item_id' => $index === 0
                        ? $latestOrderItem?->id
                        : null,
                    'status' => 'growing',
                    'planted_at' => now()->subDays(7),
                    'assigned_at' => $index === 0 && $latestOrderItem
                        ? now()
                        : null,
                    'harvested_at' => null,
                    'note' => null,
                ]
            );

            $slot->update([
                'status' => 'occupied',
            ]);
        }
    }
}