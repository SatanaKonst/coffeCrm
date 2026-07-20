<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\VkSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.vk.secret' => 'test-secret']);
        config(['services.vk.root_admin_id' => 99999]);
    }

    public function test_crm_without_sign_returns_403_with_styled_page(): void
    {
        $this->get('/crm')->assertStatus(403)->assertSee('Доступ запрещён');
    }

    public function test_admin_route_as_client_returns_403(): void
    {
        $params = ['vk_user_id' => '11111', 'vk_app_id' => '100'];
        $params['vk_sign'] = app(VkSign::class)->sign($params);
        $url = '/crm/admin/orders?'.http_build_query($params);

        $this->get($url)->assertStatus(403)->assertSee('Доступ запрещён');
    }

    public function test_inactive_product_create_returns_404(): void
    {
        $params = ['vk_user_id' => '11111', 'vk_app_id' => '100'];
        $params['vk_sign'] = app(VkSign::class)->sign($params);

        $inactive = Product::factory()->create(['is_active' => false]);

        $this->get('/crm/orders/create/'.$inactive->id.'?'.http_build_query($params))
            ->assertStatus(404)
            ->assertSee('Страница не найдена');
    }

    public function test_unknown_crm_path_returns_404(): void
    {
        $params = ['vk_user_id' => '11111', 'vk_app_id' => '100'];
        $params['vk_sign'] = app(VkSign::class)->sign($params);

        $this->get('/crm/no-such-page?'.http_build_query($params))
            ->assertStatus(404)
            ->assertSee('Страница не найдена');
    }
}
