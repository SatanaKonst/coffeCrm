<?php

namespace Tests\Feature;

use App\Enums\CoffeeVolume;
use App\Enums\OrderStatus;
use App\Enums\OrderSubscription;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Services\VkSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.vk.secret' => 'test-secret']);
    }

    private function signedUrl(string $path, array $params = []): string
    {
        $params = array_merge(['vk_user_id' => '12345', 'vk_app_id' => '100'], $params);
        $params['vk_sign'] = app(VkSign::class)->sign($params);

        return $path.'?'.http_build_query($params);
    }

    /** Валидный payload для заказа. */
    private function orderPayload(Product $product, array $override = []): array
    {
        return array_merge([
            'product_id' => $product->id,
            'qty' => 2,
            'client_name' => 'Иван Тестов',
            'client_phone' => '+7 999 123-45-67',
            'city' => 'Москва',
            'street' => 'ул. Ленина',
            'building' => '10',
            'entrance' => '2',
            'apartment' => '42',
            'intercom' => '42К',
            'comment' => 'no sugar',
            'subscription' => OrderSubscription::OneTime->value,
            'volume' => CoffeeVolume::G200->value,
            'grind' => true,
        ], $override);
    }

    public function test_create_form_requires_active_product(): void
    {
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->get($this->signedUrl('/crm/orders/create/'.$inactive->id))->assertStatus(404);
    }

    public function test_store_creates_order_with_snapshot(): void
    {
        $product = Product::factory()->create(['name' => 'Latte', 'is_active' => true]);
        // Фабрика создаёт цены (configure): g200 = $base, g500 = base*2.4, kg1 = base*4.5.
        $g200Price = $product->priceFor(CoffeeVolume::G200);
        $this->assertNotNull($g200Price, 'Фабрика должна создавать цену для g200');

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product, [
            'volume' => CoffeeVolume::G200->value,
            'qty' => 2,
        ]))->assertRedirect(route('crm.orders.index'));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::New, $order->status);
        $expected = number_format($g200Price * 2, 2, '.', '');
        $this->assertSame($expected, (string) $order->total);
        $this->assertSame('no sugar', $order->comment);
        $this->assertSame('Москва', $order->city);
        $this->assertSame(CoffeeVolume::G200, $order->volume);
        $this->assertTrue($order->grind);
        $this->assertSame(OrderSubscription::OneTime, $order->subscription);

        $item = $order->items()->first();
        $this->assertSame('Latte', $item->name);
        $this->assertSame((string) $g200Price, (string) $item->price);
        $this->assertSame(2, $item->qty);
    }

    public function test_volume_uses_product_price_for_volume(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $kg1Price = $product->priceFor(CoffeeVolume::KG1);
        $this->assertNotNull($kg1Price);

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product, [
            'volume' => CoffeeVolume::KG1->value,
            'qty' => 1,
        ]))->assertRedirect(route('crm.orders.index'));

        $order = Order::first();
        $this->assertSame((string) $kg1Price, (string) $order->total);
        $this->assertSame((string) $kg1Price, (string) $order->items()->first()->price);
    }

    public function test_unavailable_volume_rejected(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        // Удалим цену kg1 — оставим только g200 и g500.
        $product->prices()->where('volume', 'kg1')->delete();
        $this->assertNull($product->fresh()->priceFor(CoffeeVolume::KG1));

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product, [
            'volume' => CoffeeVolume::KG1->value,
        ]))->assertSessionHasErrors('volume');

        $this->assertSame(0, Order::count());
    }

    public function test_store_updates_client_profile(): void
    {
        $product = Product::factory()->create();

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product, [
            'client_name' => 'Новое Имя',
            'client_phone' => '+7 555 000-00-01',
        ]))->assertRedirect();

        $client = Client::where('vk_user_id', 12345)->first();
        $this->assertSame('Новое Имя', $client->name);
        $this->assertSame('+7 555 000-00-01', $client->phone);
    }

    public function test_store_rejects_inactive_product(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product))
            ->assertSessionHasErrors('product_id');

        $this->assertSame(0, Order::count());
    }

    public function test_required_fields_validated(): void
    {
        $product = Product::factory()->create();

        $this->post($this->signedUrl('/crm/orders'), [
            'product_id' => $product->id,
        ])->assertSessionHasErrors([
            'client_name', 'client_phone', 'city', 'street', 'building',
            'subscription', 'volume',
        ]);

        $this->assertSame(0, Order::count());
    }

    public function test_qty_must_be_positive(): void
    {
        $product = Product::factory()->create();

        $this->post($this->signedUrl('/crm/orders'), $this->orderPayload($product, ['qty' => 0]))
            ->assertSessionHasErrors('qty');

        $this->assertSame(0, Order::count());
    }

    public function test_index_lists_only_own_orders(): void
    {
        $me = Client::factory()->create(['vk_user_id' => 555]);
        $other = Client::factory()->create();

        $mine = Order::factory()->for($me)->create();
        $otherOrder = Order::factory()->for($other)->create();

        $resp = $this->get($this->signedUrl('/crm/orders', ['vk_user_id' => '555']));

        $resp->assertStatus(200)
            ->assertSee("#{$mine->id}")
            ->assertDontSee("#{$otherOrder->id}");
    }

    public function test_index_shows_placeholder_when_empty(): void
    {
        $this->get($this->signedUrl('/crm/orders'))->assertSee('У вас пока нет заказов');
    }
}
