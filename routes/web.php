<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CultivationController;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/products/{eventProduct}', [ProductController::class, 'show'])
    ->name('products.show');


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/devices', [DeviceController::class, 'index'])
        ->name('devices.index');

    Route::patch(
    '/admin/cultivations/{cultivation}/status',
    [CultivationController::class, 'updateStatus']
)->name('admin.cultivations.update-status');


    Route::get('/admin/orders', [OrderController::class, 'adminIndex'])
    ->name('admin.orders.index');

    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])
    ->name('admin.orders.update-status');

    Route::get('/admin/cultivations', [CultivationController::class, 'adminIndex'])
    ->name('admin.cultivations.index');
    
    Route::patch(
    '/admin/cultivations/{cultivation}/assign',
    [CultivationController::class, 'assignOrderItem']
)->name('admin.cultivations.assign');

    Route::get('/devices/{device}', [DeviceController::class, 'show'])
        ->name('devices.show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/products/{eventProduct}/reserve', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/events/{event}', [EventController::class, 'show'])
    ->name('events.show');

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

require __DIR__.'/auth.php';
