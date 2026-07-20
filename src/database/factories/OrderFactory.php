<?php

namespace Database\Factories;

use App\Enums\CoffeeVolume;
use App\Enums\OrderStatus;
use App\Enums\OrderSubscription;
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
            'client_name' => fake()->name(),
            'client_phone' => fake()->numerify('+7 9## ###-##-##'),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'total' => fake()->randomFloat(2, 150, 2000),
            'comment' => fake()->optional(0.3)->sentence(6),
            'city' => fake()->city(),
            'street' => fake()->streetName(),
            'building' => (string) fake()->buildingNumber(),
            'entrance' => (string) fake()->numberBetween(1, 5),
            'apartment' => (string) fake()->numberBetween(1, 200),
            'intercom' => fake()->optional(0.5)->numerify('##К'),
            'subscription' => fake()->randomElement(OrderSubscription::cases()),
            'volume' => fake()->randomElement(CoffeeVolume::cases()),
            'grind' => fake()->boolean(30),
        ];
    }
}
