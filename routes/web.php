<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\CultivationController;
use App\Http\Controllers\VisitController;
use App\Models\EventProgram;
use App\Http\Controllers\AdmissionReservationController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get(
    '/programs/{eventProgram}/reservations',
    [AdmissionReservationController::class, 'create']
)->name('admission-reservations.create');

Route::post(
    '/programs/{eventProgram}/reservations',
    [AdmissionReservationController::class, 'store']
)->name('admission-reservations.store');

Route::get(
    '/reservations/{reservationCode}',
    [AdmissionReservationController::class, 'show']
)->name('admission-reservations.show');

Route::patch(
    '/reservations/{reservationCode}/cancel',
    [AdmissionReservationController::class, 'cancel']
)->name('admission-reservations.cancel');

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
    Route::middleware(['auth', 'admin'])->group(function () {
    // 既存の管理者向けルート

    Route::get(
        '/admin/programs/{eventProgram}/visits',
        [VisitController::class, 'index']
    )->name('admin.visits.index');

    Route::post(
        '/admin/programs/{eventProgram}/visits',
        [VisitController::class, 'store']
    )->name('admin.visits.store');
Route::post(
    '/admin/reservations/{admissionReservation}/check-in',
    [VisitController::class, 'checkInReservation']
)->name('admin.admission-reservations.check-in');
    Route::patch(
        '/admin/visits/{visit}/exit',
        [VisitController::class, 'exit']
    )->name('admin.visits.exit');
});
    Route::patch(
    '/admin/cultivations/{cultivation}/assign',
    [CultivationController::class, 'assignOrderItem']
)->name('admin.cultivations.assign');

    Route::get('/devices/{device}', [DeviceController::class, 'show'])
        ->name('devices.show');
    Route::get('/admin/products', [ProductController::class, 'adminIndex'])
    ->name('admin.products.index');

Route::get('/admin/products/create', [ProductController::class, 'create'])
    ->name('admin.products.create');

Route::post('/admin/products', [ProductController::class, 'store'])
    ->name('admin.products.store');

Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])
    ->name('admin.products.edit');

Route::patch('/admin/products/{product}', [ProductController::class, 'update'])
    ->name('admin.products.update');
});

Route::get('/dashboard', function () {
    $eventPrograms = EventProgram::where('status', 'published')
        ->orderBy('start_at')
        ->get();

    return view('dashboard', compact('eventPrograms'));
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
