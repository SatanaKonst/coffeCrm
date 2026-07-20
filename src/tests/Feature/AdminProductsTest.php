<?php

namespace Tests\Feature;

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

    public function test_admin_can_create_product(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            ['name' => 'New Brew', 'description' => 'desc', 'price' => 450, 'is_active' => true],
        )->assertRedirect(route('crm.admin.products.index'));

        $this->assertDatabaseHas('products', ['name' => 'New Brew', 'price' => 450]);
    }

    public function test_name_and_price_required(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            ['name' => '', 'price' => null],
        )->assertSessionHasErrors(['name', 'price']);
    }

    public function test_negative_price_rejected(): void
    {
        $this->post(
            $this->signedUrl('/crm/admin/products', $this->adminVkId),
            ['name' => 'X', 'price' => -10],
        )->assertSessionHasErrors('price');
    }

    public function test_admin_can_update_product(): void
    {
        $p = Product::factory()->create(['name' => 'Old', 'price' => 100]);

        $this->patch(
            $this->signedUrl("/crm/admin/products/{$p->id}", $this->adminVkId),
            ['name' => 'New', 'price' => 200, 'is_active' => false],
        )->assertRedirect(route('crm.admin.products.index'));

        $p->refresh();
        $this->assertSame('New', $p->name);
        $this->assertSame('200.00', (string) $p->price);
        $this->assertFalse($p->is_active);
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
