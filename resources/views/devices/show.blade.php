<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $device->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
                <p>
                    Serial: {{ $device->serial_number }}
                </p>

                <p>
                    Status: {{ $device->status }}
                </p>

                @if ($device->description)
                    <p class="mt-2">
                        {{ $device->description }}
                    </p>
                @endif
            </div>

            <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
    <h3 class="text-lg font-semibold mb-4">
        最新センサー情報
    </h3>

    @if ($latestSensorLog)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500">気温</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->temperature }} ℃
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">湿度</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->humidity }} %
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">水温</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->water_temperature }} ℃
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">EC</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->ec }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">pH</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->ph }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">照度</p>
                <p class="font-semibold">
                    {{ number_format($latestSensorLog->light) }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">水位</p>
                <p class="font-semibold">
                    {{ $latestSensorLog->water_level }}
                </p>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500">
            計測時刻:
            {{ $latestSensorLog->measured_at }}
        </p>
    @else
        <p class="text-gray-500">
            センサーログはまだありません。
        </p>
    @endif
</div>
<div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
    <h3 class="text-lg font-semibold mb-4">
        直近24時間のセンサーログ
    </h3>

    @if ($sensorLogs->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b text-left">
                        <th class="py-2 pr-4">計測時刻</th>
                        <th class="py-2 pr-4">気温</th>
                        <th class="py-2 pr-4">湿度</th>
                        <th class="py-2 pr-4">水温</th>
                        <th class="py-2 pr-4">EC</th>
                        <th class="py-2 pr-4">pH</th>
                        <th class="py-2 pr-4">照度</th>
                        <th class="py-2 pr-4">水位</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($sensorLogs as $log)
                        <tr class="border-b">
                            <td class="py-2 pr-4">
                                {{ $log->measured_at }}
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->temperature }} ℃
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->humidity }} %
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->water_temperature }} ℃
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->ec }}
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->ph }}
                            </td>

                            <td class="py-2 pr-4">
                                {{ number_format($log->light) }}
                            </td>

                            <td class="py-2 pr-4">
                                {{ $log->water_level }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500">
            直近24時間のセンサーログはありません。
        </p>
    @endif
</div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($device->slots as $slot)
    <div class="p-4 bg-white border rounded-lg">
        <h3 class="font-semibold">
            Level {{ $slot->level }} / {{ $slot->position }}
        </h3>

        <p class="text-sm text-gray-500">
            {{ $slot->status }}
        </p>

        @forelse ($slot->cultivations as $cultivation)
            <div class="mt-3 pt-3 border-t">
                <p class="font-semibold">
                    {{ $cultivation->product->name }}
                </p>

                <p class="text-sm">
                    状態: {{ $cultivation->status }}
                </p>

                @if ($cultivation->orderItem?->order?->user)
    <p class="mt-2 text-sm">
        予約者:
        {{ $cultivation->orderItem->order->user->name }}
    </p>
@else
    <p class="mt-2 text-sm text-gray-400">
        予約者: 未割当
    </p>
@endif

                @if ($cultivation->planted_at)
                    <p class="text-sm text-gray-500">
                        播種日: {{ $cultivation->planted_at }}
                    </p>
                @endif
            </div>
        @empty
            <p class="mt-3 text-sm text-gray-400">
                栽培中の苗なし
            </p>
        @endforelse
    </div>
@endforeach
            </div>

        </div>
    </div>
</x-app-layout>