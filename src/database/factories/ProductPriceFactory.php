<?php

namespace Database\Factories;

use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'volume' => fake()->randomElement(['g200', 'g500', 'kg1']),
            'price' => fake()->randomFloat(2, 150, 2000),
        ];
    }
}
