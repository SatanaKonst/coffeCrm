<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $names = ['Эспрессо', 'Американо', 'Капучино', 'Латте', 'Раф', 'Флэт уайт', 'Мокко', 'Глясе'];

        return [
            'name' => fake()->randomElement($names).' #'.fake()->randomNumber(3),
            'description' => fake()->optional(0.8)->sentence(8),
            'price' => fake()->randomFloat(2, 150, 600),
            'is_active' => true,
        ];
    }
}
