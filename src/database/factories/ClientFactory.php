<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vk_user_id' => fake()->unique()->randomNumber(9, true),
            'name' => fake()->name(),
            'phone' => fake()->optional(0.7)->e164PhoneNumber(),
        ];
    }
}
