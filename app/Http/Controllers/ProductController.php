<?php

namespace App\Http\Controllers;

use App\Models\EventProduct;
use Illuminate\Http\Request;

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
    public function show(EventProduct $eventProduct)
{
    $eventProduct->load(['event', 'product']);

    return view('products.show', compact('eventProduct'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}