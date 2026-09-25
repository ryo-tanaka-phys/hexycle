<?php

namespace Database\Factories;

use App\Models\Cultivation;
use App\Models\Product;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cultivation>
 */
class CultivationFactory extends Factory
{
    protected $model = Cultivation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'slot_id' => Slot::factory(),
            'order_item_id' => null,
            'status' => 'growing',
            'planted_at' => now()->subDays(7),
            'assigned_at' => null,
            'harvested_at' => null,
            'note' => null,
        ];
    }

    

}
