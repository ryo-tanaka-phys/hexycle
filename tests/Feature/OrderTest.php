<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\OrderItem;

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
public function test_user_cannot_exceed_reservation_limit_across_multiple_orders(): void
{
    $user = User::factory()->create();

    $event = Event::factory()->create([
        'reservation_start_at' => now()->subHour(),
        'reservation_end_at' => now()->addHour(),
    ]);

    $eventProduct = EventProduct::factory()->create([
        'event_id' => $event->id,
        'stock' => 10,
        'reservation_limit' => 3,
    ]);

    $this
        ->actingAs($user)
        ->post(
            route('orders.store', $eventProduct),
            [
                'quantity' => 2,
            ]
        )
        ->assertSessionHasNoErrors();

    $response = $this
        ->actingAs($user)
        ->post(
            route('orders.store', $eventProduct),
            [
                'quantity' => 2,
            ]
        );

    $response->assertSessionHasErrors('quantity');

    $this->assertSame(
    2,
    (int) OrderItem::where('event_product_id', $eventProduct->id)
        ->sum('quantity')
);
}
public function test_reservation_cannot_be_made_before_reservation_start(): void
{
    $user = User::factory()->create();

    $event = Event::factory()->create([
        'reservation_start_at' => now()->addHour(),
        'reservation_end_at' => now()->addHours(2),
    ]);

    $eventProduct = EventProduct::factory()->create([
        'event_id' => $event->id,
        'stock' => 10,
        'reservation_limit' => 3,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('orders.store', $eventProduct),
            [
                'quantity' => 1,
            ]
        );

    $response->assertSessionHasErrors('reservation');

    $this->assertDatabaseMissing('orders', [
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);
}
public function test_reservation_cannot_be_made_after_reservation_end(): void
{
    $user = User::factory()->create();

    $event = Event::factory()->create([
        'reservation_start_at' => now()->subHours(2),
        'reservation_end_at' => now()->subHour(),
    ]);

    $eventProduct = EventProduct::factory()->create([
        'event_id' => $event->id,
        'stock' => 10,
        'reservation_limit' => 3,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('orders.store', $eventProduct),
            [
                'quantity' => 1,
            ]
        );

    $response->assertSessionHasErrors('reservation');

    $this->assertDatabaseMissing('orders', [
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);
}
public function test_reservation_cannot_exceed_stock(): void
{
    $user = User::factory()->create();

    $event = Event::factory()->create([
        'reservation_start_at' => now()->subHour(),
        'reservation_end_at' => now()->addHour(),
    ]);

    $eventProduct = EventProduct::factory()->create([
        'event_id' => $event->id,
        'stock' => 1,
        'reservation_limit' => 5,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(
            route('orders.store', $eventProduct),
            [
                'quantity' => 2,
            ]
        );

    $response->assertSessionHasErrors('quantity');

    $this->assertDatabaseMissing('orders', [
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);

    $eventProduct->refresh();

    $this->assertSame(
        1,
        $eventProduct->stock
    );
}
public function test_cancelling_order_restores_stock(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create();

    $event = Event::factory()->create();

    $eventProduct = EventProduct::factory()->create([
        'event_id' => $event->id,
        'stock' => 8,
        'reservation_limit' => 5,
    ]);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $order->items()->create([
        'event_product_id' => $eventProduct->id,
        'quantity' => 2,
        'unit_price' => $eventProduct->price,
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(
            route('admin.orders.update-status', $order),
            [
                'status' => 'cancelled',
            ]
        );

    $response->assertSessionHasNoErrors();

    $order->refresh();
    $eventProduct->refresh();

    $this->assertSame(
        'cancelled',
        $order->status
    );

    $this->assertSame(
        10,
        $eventProduct->stock
    );
}
}