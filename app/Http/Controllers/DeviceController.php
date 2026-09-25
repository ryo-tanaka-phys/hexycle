<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $devices = Device::withCount('slots')
        ->orderBy('name')
        ->get();

    return view('devices.index', compact('devices'));
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
    public function show(Device $device)
{
    $device->load([
        'slots' => function ($query) {
            $query->with([
                'cultivations.product',
                'cultivations.orderItem.order.user',
            ])
            ->orderBy('level')
            ->orderBy('position');
        },
    ]);

    $latestSensorLog = $device->sensorLogs()
        ->latest('measured_at')
        ->first();

    $sensorLogs = $device->sensorLogs()
        ->where('measured_at', '>=', now()->subHours(24))
        ->orderByDesc('measured_at')
        ->get();

    return view('devices.show', compact(
        'device',
        'latestSensorLog',
        'sensorLogs'
    ));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Device $device)
    {
        //
    }
}
