<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

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
            'is_active' => true,
        ];
    }

    /**
     * Создать цены трёх объёмов (g200 — базовая, g500 = x2.4, kg1 = x4.5).
     *
     * ponytail: коэффициенты — только для тестовых данных/seeder.
     * В реальном UI цены задаёт админ.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product): void {
            $base = fake()->randomFloat(2, 150, 600);
            $prices = [
                ['volume' => 'g200', 'price' => $base],
                ['volume' => 'g500', 'price' => round($base * 2.4, 2)],
                ['volume' => 'kg1', 'price' => round($base * 4.5, 2)],
            ];

            Collection::make($prices)->each(fn (array $p) => $product->prices()->create($p));
        });
    }
}
