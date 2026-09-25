<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_reserve_product(): void
    {
        $user = User::factory()->create();

        $event = Event::factory()->create();

        $product = Product::factory()->create();

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 300,
            'stock' => 20,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store', $eventProduct), [
                'quantity' => 1,
            ]);

        $response->assertRedirect(
            route('products.show', $eventProduct)
        );

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'reserved',
        ]);

        $this->assertDatabaseHas('order_items', [
            'event_product_id' => $eventProduct->id,
            'quantity' => 1,
            'unit_price' => 300,
        ]);
    }

    public function test_reservation_decreases_stock(): void
    {
        $user = User::factory()->create();

        $event = Event::factory()->create();

        $product = Product::factory()->create();

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 300,
            'stock' => 20,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $this
            ->actingAs($user)
            ->post(route('orders.store', $eventProduct), [
                'quantity' => 2,
            ]);

        $this->assertDatabaseHas('event_products', [
            'id' => $eventProduct->id,
            'stock' => 18,
        ]);
    }

    public function test_user_can_see_own_order_history(): void
    {
        $user = User::factory()->create();

        $event = Event::factory()->create([
            'name' => 'QSHOP文化祭 2026',
        ]);

        $product = Product::factory()->create([
            'name' => 'サニーレタス',
        ]);

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 300,
            'stock' => 20,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $order->items()->create([
            'event_product_id' => $eventProduct->id,
            'quantity' => 1,
            'unit_price' => 300,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('QSHOP文化祭 2026');
        $response->assertSee('サニーレタス');
    }

    public function test_user_cannot_see_another_users_order_history(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $event = Event::factory()->create();

        $product = Product::factory()->create([
            'name' => '他人のバジル予約',
        ]);

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 250,
            'stock' => 15,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $order = Order::create([
            'user_id' => $anotherUser->id,
            'event_id' => $event->id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $order->items()->create([
            'event_product_id' => $eventProduct->id,
            'quantity' => 1,
            'unit_price' => 250,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertDontSee('他人のバジル予約');
    }
    public function test_admin_can_update_order_status(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create();

    $event = Event::factory()->create();

    $order = Order::create([
        'user_id' => $customer->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
        ]);

    $response->assertRedirect(route('admin.orders.index'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'confirmed',
    ]);
}

public function test_customer_cannot_update_order_status(): void
{
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $event = Event::factory()->create();

    $order = Order::create([
        'user_id' => $customer->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $response = $this
        ->actingAs($customer)
        ->patch(route('admin.orders.update-status', $order), [
            'status' => 'confirmed',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'reserved',
    ]);
}
}