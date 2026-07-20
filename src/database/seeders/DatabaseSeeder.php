<?php

namespace Database\Seeders;

use App\Enums\CoffeeVolume;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Product::factory() создаёт и цены (см. configure()).
        $products = Product::factory(8)->create();

        Client::factory(10)->create()->each(function (Client $client) use ($products) {
            $ordersCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $ordersCount; $i++) {
                $order = Order::factory()->for($client)->create();

                $itemsCount = fake()->numberBetween(1, 4);
                $total = 0;

                for ($j = 0; $j < $itemsCount; $j++) {
                    $product = $products->random();
                    $qty = fake()->numberBetween(1, 3);
                    $volume = fake()->randomElement(CoffeeVolume::cases());
                    $unitPrice = $product->priceFor($volume) ?? (float) $product->prices->first()->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $unitPrice,
                        'qty' => $qty,
                    ]);

                    $total += $unitPrice * $qty;
                }

                $order->update(['total' => $total]);
            }
        });

        $adminVkId = (int) env('VK_ROOT_ADMIN_ID', 0);
        if ($adminVkId > 0) {
            Client::firstOrCreate(
                ['vk_user_id' => $adminVkId],
                ['name' => 'Администратор'],
            );
        }
    }
}
