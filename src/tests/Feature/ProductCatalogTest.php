<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\VkSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductCatalogTest extends TestCase
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

    public function test_guest_without_sign_gets_403(): void
    {
        $this->get('/crm/products')->assertStatus(403);
    }

    public function test_authenticated_client_sees_active_products_only(): void
    {
        $active = Product::factory()->create(['name' => 'Latte Test', 'is_active' => true]);
        $inactive = Product::factory()->create(['name' => 'Hidden One', 'is_active' => false]);

        $resp = $this->get($this->signedUrl('/crm/products'));

        $resp->assertStatus(200)
            ->assertSee($active->name)
            ->assertDontSee($inactive->name);
    }

    public function test_empty_catalog_shows_placeholder(): void
    {
        $resp = $this->get($this->signedUrl('/crm/products'));

        $resp->assertStatus(200)
            ->assertSee('Ассортимент скоро появится');
    }
}
