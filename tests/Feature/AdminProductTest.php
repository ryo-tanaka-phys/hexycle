<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_product_management_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Product::factory()->create([
            'name' => 'サニーレタス',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('管理者向け商品一覧');
        $response->assertSee('サニーレタス');
    }

    public function test_customer_cannot_view_product_management_page(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this
            ->actingAs($customer)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'ルッコラ',
                'variety' => 'ロケット',
                'description' => '文化祭販売用の商品です。',
                'is_active' => true,
            ]);

        $response->assertRedirect(
            route('admin.products.index')
        );

        $this->assertDatabaseHas('products', [
            'name' => 'ルッコラ',
            'variety' => 'ロケット',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create([
            'name' => 'ルッコラ',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(
                route('admin.products.update', $product),
                [
                    'name' => 'ルッコラ',
                    'variety' => 'ロケット',
                    'description' => '更新後の説明です。',
                    'is_active' => false,
                ]
            );

        $response->assertRedirect(
            route('admin.products.index')
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'description' => '更新後の説明です。',
            'is_active' => false,
        ]);
    }
}
