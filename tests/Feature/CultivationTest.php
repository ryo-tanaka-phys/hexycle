<?php

namespace Tests\Feature;

use App\Models\Cultivation;
use App\Models\Device;
use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CultivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cultivation_belongs_to_product(): void
    {
        $product = Product::factory()->create();
        $cultivation = Cultivation::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertTrue(
            $cultivation->product->is($product)
        );
    }

    public function test_cultivation_belongs_to_slot(): void
    {
        $slot = Slot::factory()->create();
        $cultivation = Cultivation::factory()->create([
            'slot_id' => $slot->id,
        ]);

        $this->assertTrue(
            $cultivation->slot->is($slot)
        );
    }

    public function test_cultivation_can_belong_to_order_item(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $product = Product::factory()->create();

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 300,
            'stock' => 10,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'event_product_id' => $eventProduct->id,
            'quantity' => 1,
            'unit_price' => 300,
        ]);

        $cultivation = Cultivation::factory()->create([
            'product_id' => $product->id,
            'order_item_id' => $orderItem->id,
        ]);

        $this->assertTrue(
            $cultivation->orderItem->is($orderItem)
        );
    }

    public function test_user_can_see_own_cultivation_information_in_order_history(): void
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
            'stock' => 10,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'event_product_id' => $eventProduct->id,
            'quantity' => 1,
            'unit_price' => 300,
        ]);

        $device = Device::factory()->create([
            'name' => 'Hexycle-01',
        ]);

        $slot = Slot::factory()->create([
            'device_id' => $device->id,
            'level' => 1,
            'position' => 'A',
            'status' => 'occupied',
        ]);

        Cultivation::factory()->create([
            'product_id' => $product->id,
            'slot_id' => $slot->id,
            'order_item_id' => $orderItem->id,
            'status' => 'growing',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('サニーレタス');
        $response->assertSee('growing');
        $response->assertSee('Hexycle-01');
        $response->assertSee('Level 1');
        $response->assertSee('A');
    }
    public function test_admin_can_update_cultivation_status(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $cultivation = Cultivation::factory()->create([
        'status' => 'growing',
        'harvested_at' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(
            route('admin.cultivations.update-status', $cultivation),
            [
                'status' => 'harvested',
            ]
        );

    $response->assertRedirect(
        route('admin.cultivations.index')
    );

    $cultivation->refresh();

    $this->assertSame(
        'harvested',
        $cultivation->status
    );

    $this->assertNotNull(
        $cultivation->harvested_at
    );
}

public function test_customer_cannot_update_cultivation_status(): void
{
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $cultivation = Cultivation::factory()->create([
        'status' => 'growing',
    ]);

    $response = $this
        ->actingAs($customer)
        ->patch(
            route('admin.cultivations.update-status', $cultivation),
            [
                'status' => 'harvested',
            ]
        );

    $response->assertForbidden();

    $cultivation->refresh();

    $this->assertSame(
        'growing',
        $cultivation->status
    );
}
public function test_admin_can_assign_cultivation_to_order_item(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create();

    $event = Event::factory()->create();
    $product = Product::factory()->create();

    $eventProduct = EventProduct::create([
        'event_id' => $event->id,
        'product_id' => $product->id,
        'price' => 300,
        'stock' => 10,
        'reservation_limit' => 3,
        'is_available' => true,
    ]);

    $order = Order::create([
        'user_id' => $customer->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'event_product_id' => $eventProduct->id,
        'quantity' => 1,
        'unit_price' => 300,
    ]);

    $cultivation = Cultivation::factory()->create([
        'product_id' => $product->id,
        'order_item_id' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(
            route('admin.cultivations.assign', $cultivation),
            [
                'order_item_id' => $orderItem->id,
            ]
        );

    $response->assertRedirect(
        route('admin.cultivations.index')
    );

    $cultivation->refresh();

    $this->assertSame(
        $orderItem->id,
        $cultivation->order_item_id
    );

    $this->assertNotNull(
        $cultivation->assigned_at
    );
}
public function test_cultivation_cannot_be_assigned_to_different_product_order_item(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create();

    $event = Event::factory()->create();

    $cultivationProduct = Product::factory()->create([
        'name' => 'サニーレタス',
    ]);

    $orderedProduct = Product::factory()->create([
        'name' => 'バジル',
    ]);

    $eventProduct = EventProduct::create([
        'event_id' => $event->id,
        'product_id' => $orderedProduct->id,
        'price' => 300,
        'stock' => 10,
        'reservation_limit' => 3,
        'is_available' => true,
    ]);

    $order = Order::create([
        'user_id' => $customer->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'event_product_id' => $eventProduct->id,
        'quantity' => 1,
        'unit_price' => 300,
    ]);

    $cultivation = Cultivation::factory()->create([
        'product_id' => $cultivationProduct->id,
        'order_item_id' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(
            route('admin.cultivations.assign', $cultivation),
            [
                'order_item_id' => $orderItem->id,
            ]
        );

    $response->assertSessionHasErrors('order_item_id');

    $cultivation->refresh();

    $this->assertNull(
        $cultivation->order_item_id
    );
}

public function test_cultivation_cannot_exceed_order_item_quantity(): void
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create();

    $event = Event::factory()->create();
    $product = Product::factory()->create();

    $eventProduct = EventProduct::create([
        'event_id' => $event->id,
        'product_id' => $product->id,
        'price' => 300,
        'stock' => 10,
        'reservation_limit' => 3,
        'is_available' => true,
    ]);

    $order = Order::create([
        'user_id' => $customer->id,
        'event_id' => $event->id,
        'status' => 'reserved',
        'ordered_at' => now(),
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'event_product_id' => $eventProduct->id,
        'quantity' => 1,
        'unit_price' => 300,
    ]);

    Cultivation::factory()->create([
        'product_id' => $product->id,
        'order_item_id' => $orderItem->id,
    ]);

    $secondCultivation = Cultivation::factory()->create([
        'product_id' => $product->id,
        'order_item_id' => null,
    ]);

    $response = $this
        ->actingAs($admin)
        ->patch(
            route('admin.cultivations.assign', $secondCultivation),
            [
                'order_item_id' => $orderItem->id,
            ]
        );

    $response->assertSessionHasErrors('order_item_id');

    $secondCultivation->refresh();

    $this->assertNull(
        $secondCultivation->order_item_id
    );
}
}