<?php

namespace Tests\Feature;

use App\Enums\CoffeeVolume;
use App\Models\Product;
use App\Services\VkSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminProductsTest extends TestCase
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

    /** Минимальный валидный payload с ценами. */
    private function pricesPayload(array $override = []): array
    {
        return array_merge([
            'name' => 'New Brew',
            'description' => 'desc',
            'is_active' => true,
            'prices' => [
                ['volume' => CoffeeVolume::G200->value, 'price' => 300],
                ['volume' => CoffeeVolume::G500->value, 'price' => 700],
                ['volume' => CoffeeVolume::KG1->value, 'price' => 1400],
            ],
        ], $override);
    }

    public function test_non_admin_gets_403_on_index(): void
    {
        $this->get($this->signedUrl('/crm/admin/products', $this->clientVkId))->assertStatus(403);
    }

    public function test_admin_sees_products(): void
    {
        $p = Product::factory()->create(['name' => 'Flat White Test']);

        $this->get($this->signedUrl('/crm/admin/products', $this->adminVkId))
            ->assertStatus(200)
            ->assertSee($p->name);
    }

    public function test_admin_can_create_product_with_prices(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            $this->pricesPayload(['name' => 'New Brew']),
        )->assertRedirect(route('crm.admin.products.index'));

        $product = Product::where('name', 'New Brew')->first();
        $this->assertNotNull($product);
        $this->assertCount(3, $product->prices);
        $this->assertSame('300.00', (string) $product->priceFor(CoffeeVolume::G200));
        $this->assertSame('1400.00', (string) $product->priceFor(CoffeeVolume::KG1));
    }

    public function test_name_required(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            $this->pricesPayload(['name' => '']),
        )->assertSessionHasErrors('name');
    }

    public function test_prices_required(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            $this->pricesPayload(['prices' => []]),
        )->assertSessionHasErrors('prices');
    }

    public function test_negative_price_rejected(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            $this->pricesPayload(['prices' => [
                ['volume' => 'g200', 'price' => -10],
            ]]),
        )->assertSessionHasErrors('prices.0.price');
    }

    public function test_admin_can_update_product_and_prices(): void
    {
        $p = Product::factory()->create(['name' => 'Old', 'is_active' => true]);

        $this->patch(
            $this->signedUrl("/crm/admin/products/{$p->id}", $this->adminVkId),
            $this->pricesPayload([
                'name' => 'New',
                'is_active' => false,
                'prices' => [
                    ['volume' => 'g200', 'price' => 500],
                    ['volume' => 'g500', 'price' => 1200],
                ],
            ]),
        )->assertRedirect(route('crm.admin.products.index'));

        $p->refresh()->load('prices');
        $this->assertSame('New', $p->name);
        $this->assertFalse($p->is_active);
        $this->assertCount(2, $p->prices); // старые удалены, 2 новых
        $this->assertSame('500.00', (string) $p->priceFor(CoffeeVolume::G200));
        $this->assertNull($p->priceFor(CoffeeVolume::KG1)); // kg1 больше не задан
    }

    public function test_admin_can_delete_product(): void
    {
        $p = Product::factory()->create();

        $this->delete($this->signedUrl("/crm/admin/products/{$p->id}", $this->adminVkId))
            ->assertRedirect(route('crm.admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $p->id]);
    }

    public function test_inactive_hidden_by_default(): void
    {
        $active = Product::factory()->create(['name' => 'Visible', 'is_active' => true]);
        $hidden = Product::factory()->create(['name' => 'HiddenInactive', 'is_active' => false]);

        $this->get($this->signedUrl('/crm/admin/products', $this->adminVkId))
            ->assertSee($active->name)
            ->assertDontSee($hidden->name);

        $this->get($this->signedUrl('/crm/admin/products', $this->adminVkId, ['inactive' => '1']))
            ->assertSee($active->name)
            ->assertSee($hidden->name);
    }
}
