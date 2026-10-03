<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventProduct>
 */
class EventProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'product_id' => Product::factory(),
            'price' => 500,
            'stock' => 10,
            'reservation_limit' => 3,
            'is_available' => true,
        ];
    }
}