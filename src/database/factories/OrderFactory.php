<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'total' => fake()->randomFloat(2, 150, 2000),
            'comment' => fake()->optional(0.3)->realText(100),
        ];
    }
}
