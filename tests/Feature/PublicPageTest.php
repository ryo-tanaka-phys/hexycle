<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Hexycle');
    }

    public function test_products_page_displays_available_products(): void
    {
        $event = Event::factory()->create();

        $product = Product::factory()->create([
            'name' => 'サニーレタス',
        ]);

        EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 300,
            'stock' => 20,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('サニーレタス');
        $response->assertSee('300');
    }

    public function test_product_detail_page_is_displayed(): void
    {
        $event = Event::factory()->create();

        $product = Product::factory()->create([
            'name' => 'バジル',
        ]);

        $eventProduct = EventProduct::create([
            'event_id' => $event->id,
            'product_id' => $product->id,
            'price' => 250,
            'stock' => 15,
            'reservation_limit' => 3,
            'is_available' => true,
        ]);

        $response = $this->get(
            route('products.show', $eventProduct)
        );

        $response->assertStatus(200);
        $response->assertSee('バジル');
        $response->assertSee('250');
    }

    public function test_event_detail_page_is_displayed(): void
    {
        $event = Event::factory()->create([
            'name' => 'QSHOP文化祭 2026',
        ]);

        $response = $this->get(
            route('events.show', $event)
        );

        $response->assertStatus(200);
        $response->assertSee('QSHOP文化祭 2026');
    }
}