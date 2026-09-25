<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->paragraph(),
            'variety' => fake()->word(),
            'image_path' => null,
            'is_active' => true,
        ];
    }
}