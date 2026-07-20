<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Services\VkSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    private int $adminVkId = 99999;

    private int $clientVkId = 11111;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.vk.secret' => 'test-secret']);
        config(['services.vk.root_admin_id' => $this->adminVkId]);
    }

    private function signedUrl(string $path, int $vkUserId, array $params = []): string
    {
        $params = array_merge(['vk_user_id' => (string) $vkUserId, 'vk_app_id' => '100'], $params);
        $params['vk_sign'] = app(VkSign::class)->sign($params);

        return $path.'?'.http_build_query($params);
    }

    public function test_non_admin_gets_403(): void
    {
        $this->get($this->signedUrl('/crm/admin/orders', $this->clientVkId))->assertStatus(403);
    }

    public function test_admin_sees_all_orders(): void
    {
        $a = Order::factory()->for(Client::factory())->create();
        $b = Order::factory()->for(Client::factory())->create();

        $this->get($this->signedUrl('/crm/admin/orders', $this->adminVkId))
            ->assertStatus(200)
            ->assertSee("#{$a->id}")
            ->assertSee("#{$b->id}");
    }

    public function test_filter_by_status(): void
    {
        $new = Order::factory()->for(Client::factory())->create(['status' => OrderStatus::New]);
        $delivered = Order::factory()->for(Client::factory())->create(['status' => OrderStatus::Delivered]);

        $this->get($this->signedUrl('/crm/admin/orders', $this->adminVkId, ['status' => 'delivered']))
            ->assertStatus(200)
            ->assertDontSee("#{$new->id}")
            ->assertSee("#{$delivered->id}");
    }

    public function test_admin_can_change_status(): void
    {
        $order = Order::factory()->for(Client::factory())->create(['status' => OrderStatus::New]);

        $this->patch(
            $this->signedUrl("/crm/admin/orders/{$order->id}", $this->adminVkId),
            ['status' => 'confirmed'],
        )->assertRedirect(route('crm.admin.orders.index', ['status' => 'confirmed']));

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
    }

    public function test_invalid_status_rejected(): void
    {
        $order = Order::factory()->for(Client::factory())->create();

        $this->patch(
            $this->signedUrl("/crm/admin/orders/{$order->id}", $this->adminVkId),
            ['status' => 'invalid'],
        )->assertSessionHasErrors('status');

        $this->assertSame($order->status->value, $order->fresh()->status->value);
    }
}
