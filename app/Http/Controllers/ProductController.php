<?php

namespace App\Http\Controllers;

use App\Models\EventProduct;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventProducts = EventProduct::with(['event', 'product'])
            ->where('is_available', true)
            ->get();

        return view('products.index', compact('eventProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('admin.products.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'variety' => ['nullable', 'string', 'max:255'],
        'is_active' => ['required', 'boolean'],
    ]);

    Product::create($validated);

    return redirect()
        ->route('admin.products.index')
        ->with('success', '商品を登録しました。');
}

    /**
     * Display the specified resource.
     */
    public function show(EventProduct $eventProduct)
{
    $eventProduct->load(['event', 'product']);

    return view('products.show', compact('eventProduct'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
{
    return view('admin.products.edit', compact('product'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'variety' => ['nullable', 'string', 'max:255'],
        'is_active' => ['required', 'boolean'],
    ]);

    $product->update($validated);

    return redirect()
        ->route('admin.products.index')
        ->with('success', '商品情報を更新しました。');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
public function adminIndex()
{
    $products = Product::latest()
        ->paginate(20);

    return view('admin.products.index', compact('products'));
}
}