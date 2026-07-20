<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
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

    public function test_create_form_requires_active_product(): void
    {
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->get($this->signedUrl('/crm/orders/create/'.$inactive->id))->assertStatus(404);
    }

    public function test_store_creates_order_with_snapshot(): void
    {
        $product = Product::factory()->create(['name' => 'Latte', 'price' => 300, 'is_active' => true]);

        $this->post(
            $this->signedUrl('/crm/orders'),
            ['product_id' => $product->id, 'qty' => 2, 'comment' => 'no sugar'],
        )->assertRedirect(route('crm.orders.index'));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::New, $order->status);
        $this->assertSame('600.00', (string) $order->total); // 300 * 2
        $this->assertSame('no sugar', $order->comment);

        $item = $order->items()->first();
        $this->assertNotNull($item);
        $this->assertSame('Latte', $item->name);              // snapshot
        $this->assertSame('300.00', (string) $item->price);  // snapshot
        $this->assertSame(2, $item->qty);

        $client = Client::where('vk_user_id', 12345)->first();
        $this->assertSame($client->id, $order->client_id);
    }

    public function test_store_rejects_inactive_product(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->post(
            $this->signedUrl('/crm/orders'),
            ['product_id' => $product->id, 'qty' => 1],
        )->assertSessionHasErrors('product_id');

        $this->assertSame(0, Order::count());
    }

    public function test_qty_must_be_positive(): void
    {
        $product = Product::factory()->create();

        $this->post(
            $this->signedUrl('/crm/orders'),
            ['product_id' => $product->id, 'qty' => 0],
        )->assertSessionHasErrors('qty');

        $this->assertSame(0, Order::count());
    }

    public function test_index_lists_only_own_orders(): void
    {
        $me = Client::factory()->create(['vk_user_id' => 555]);
        $other = Client::factory()->create();

        $mine = Order::factory()->for($me)->create();
        Order::factory()->for($other)->create();

        $resp = $this->get($this->signedUrl('/crm/orders', ['vk_user_id' => '555']));

        $resp->assertStatus(200)
            ->assertSee("#{$mine->id}")
            ->assertDontSee('#'.Order::where('client_id', $other->id)->value('id'));
    }

    public function test_index_shows_placeholder_when_empty(): void
    {
        $this->get($this->signedUrl('/crm/orders'))->assertSee('У вас пока нет заказов');
    }
}
