<?php

namespace App\Http\Controllers;

use App\Models\Cultivation;
use Illuminate\Http\Request;
use App\Models\OrderItem;

class CultivationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cultivation $cultivation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cultivation $cultivation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cultivation $cultivation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cultivation $cultivation)
    {
        //
    }

    public function adminIndex()
{
    $cultivations = Cultivation::with([
        'product',
        'slot.device',
        'orderItem.order.user',
    ])
    ->latest()
    ->paginate(20);

    $orderItems = OrderItem::with([
        'order.user',
        'eventProduct.product',
    ])
    ->latest()
    ->get();

    return view('admin.cultivations.index', compact(
        'cultivations',
        'orderItems'
    ));
}
public function updateStatus(Request $request, Cultivation $cultivation)
{
    $validated = $request->validate([
        'status' => [
            'required',
            'in:growing,ready,harvested,failed',
        ],
    ]);

    $cultivation->update([
        'status' => $validated['status'],
        'harvested_at' => $validated['status'] === 'harvested'
            ? now()
            : $cultivation->harvested_at,
    ]);

    return redirect()
        ->route('admin.cultivations.index')
        ->with('success', '栽培状態を更新しました。');
}
public function assignOrderItem(Request $request, Cultivation $cultivation)
{
    $validated = $request->validate([
        'order_item_id' => [
            'nullable',
            'exists:order_items,id',
        ],
    ]);

    $orderItem = null;

    if (! empty($validated['order_item_id'])) {
        $orderItem = OrderItem::with('eventProduct')
            ->findOrFail($validated['order_item_id']);

        if ($orderItem->eventProduct->product_id !== $cultivation->product_id) {
            return back()->withErrors([
                'order_item_id' => '栽培株の商品と注文商品の種類が一致していません。',
            ]);
        }

        $assignedCount = Cultivation::where(
            'order_item_id',
            $orderItem->id
        )
        ->where('id', '!=', $cultivation->id)
        ->count();

        if ($assignedCount >= $orderItem->quantity) {
            return back()->withErrors([
                'order_item_id' => 'この注文には必要数の栽培株がすでに割り当てられています。',
            ]);
        }
    }

    $cultivation->update([
        'order_item_id' => $orderItem?->id,
        'assigned_at' => $orderItem ? now() : null,
    ]);

    return redirect()
        ->route('admin.cultivations.index')
        ->with('success', '栽培株の割り当てを更新しました。');
}
}
