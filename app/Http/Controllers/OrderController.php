<?php

namespace App\Http\Controllers;

use App\Models\EventProduct;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;

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

    $event = $eventProduct->event;

    if (
        $event->reservation_start_at !== null
        && now()->lt($event->reservation_start_at)
    ) {
        return back()->withErrors([
            'reservation' => '予約受付はまだ開始されていません。',
        ])->withInput();
    }

    if (
        $event->reservation_end_at !== null
        && now()->gt($event->reservation_end_at)
    ) {
        return back()->withErrors([
            'reservation' => '予約受付は終了しています。',
        ])->withInput();
    }

    $result = DB::transaction(function () use ($request, $eventProduct) {
        $lockedEventProduct = EventProduct::whereKey($eventProduct->id)
            ->lockForUpdate()
            ->firstOrFail();

        $alreadyReserved = OrderItem::where(
            'event_product_id',
            $lockedEventProduct->id
        )
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('status', '!=', 'cancelled');
            })
            ->sum('quantity');

        if (
            $lockedEventProduct->reservation_limit !== null
            && $alreadyReserved + $request->quantity
                > $lockedEventProduct->reservation_limit
        ) {
            return 'reservation_limit_exceeded';
        }

        if ($request->quantity > $lockedEventProduct->stock) {
            return 'stock_shortage';
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'event_id' => $lockedEventProduct->event_id,
            'status' => 'reserved',
            'ordered_at' => now(),
        ]);

        $order->items()->create([
            'event_product_id' => $lockedEventProduct->id,
            'quantity' => $request->quantity,
            'unit_price' => $lockedEventProduct->price,
        ]);

        $lockedEventProduct->decrement(
            'stock',
            $request->quantity
        );

        return 'success';
    });

    if ($result === 'reservation_limit_exceeded') {
        return back()->withErrors([
            'quantity' => '予約数量が上限を超えています。',
        ])->withInput();
    }

    if ($result === 'stock_shortage') {
        return back()->withErrors([
            'quantity' => '在庫が不足しています。',
        ])->withInput();
    }

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
if ($validated['status'] === 'ready') {
    $order->load('items.cultivations');

    $allReady = $order->items->isNotEmpty()
        && $order->items->every(function ($item) {
            $assignedCount = $item->cultivations->count();

            $unassignedCount = max(
                $item->quantity - $assignedCount,
                0
            );

            $readyCount = $item->cultivations
                ->where('status', 'ready')
                ->count();

            return
                $unassignedCount === 0
                && $assignedCount > 0
                && $readyCount === $assignedCount;
        });

    if (! $allReady) {
        return back()->withErrors([
            'status' => 'すべての栽培株が受け渡し準備完了になるまで、注文を準備完了にはできません。',
        ]);
    }
}
    DB::transaction(function () use ($order, $validated) {
        $order->load('items.eventProduct');

        $wasCancelled = $order->status === 'cancelled';
        $willBeCancelled = $validated['status'] === 'cancelled';

        if (! $wasCancelled && $willBeCancelled) {
            foreach ($order->items as $item) {
                $eventProduct = EventProduct::whereKey(
                    $item->event_product_id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                $eventProduct->increment(
                    'stock',
                    $item->quantity
                );
            }
        }

        $order->update([
            'status' => $validated['status'],
        ]);
    });

    return redirect()
        ->route('admin.orders.index')
        ->with('success', '注文状態を更新しました。');
}
}


