<?php

namespace App\Http\Controllers;

use App\Models\EventProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $orders = auth()->user()
        ->orders()
        ->with([
            'event',
            'items.eventProduct.product',
            'items.cultivations.slot.device',
        ])
        ->latest('ordered_at')
        ->paginate(10);

    return view('orders.index', compact('orders'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


public function store(Request $request, EventProduct $eventProduct)
{
    $request->validate([
        'quantity' => [
            'required',
            'integer',
            'min:1',
            'max:' . min(
                $eventProduct->stock,
                $eventProduct->reservation_limit ?? $eventProduct->stock
            ),
        ],
    ]);

    DB::transaction(function () use ($request, $eventProduct) {
        $order = Order::create([
            'user_id' => auth()->id(),
            'event_id' => $eventProduct->event_id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $order->items()->create([
            'event_product_id' => $eventProduct->id,
            'quantity' => $request->quantity,
            'unit_price' => $eventProduct->price,
        ]);

        $eventProduct->decrement('stock', $request->quantity);
    });

    return redirect()
        ->route('products.show', $eventProduct)
        ->with('success', '苗を予約しました。');
}
    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
    public function adminIndex()
{
    $orders = Order::with([
        'user',
        'event',
        'items.eventProduct.product',
        'items.cultivations.slot.device',
    ])
    ->latest('ordered_at')
    ->paginate(20);

    return view('admin.orders.index', compact('orders'));
}
public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => [
            'required',
            'in:reserved,confirmed,ready,completed,cancelled',
        ],
    ]);

    $order->update([
        'status' => $validated['status'],
    ]);

    return redirect()
        ->route('admin.orders.index')
        ->with('success', '注文状態を更新しました。');
}
}
