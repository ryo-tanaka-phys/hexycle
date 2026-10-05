<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            管理者向け注文一覧
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

           @forelse ($orders as $order)
    @php
    $orderUnassignedCount = $order->items->sum(function ($item) {
        return max(
            $item->quantity - $item->cultivations->count(),
            0
        );
    });

    $orderAllReady =
        $order->items->isNotEmpty()
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
@endphp

    <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
                    <div class="mb-4">
                        <p class="font-semibold">
                            注文 #{{ $order->id }}
                        </p>
                        @if ($orderAllReady)
    <div class="mt-2 p-3 bg-green-50 text-green-700 rounded">
        この注文は受け渡し準備可能です。
    </div>
@else
    <div class="mt-2 p-3 bg-yellow-50 text-yellow-700 rounded">
        この注文はまだ受け渡し準備が完了していません。
    </div>
@endif
                        @if ($orderUnassignedCount > 0)
    <div class="mt-2 p-3 bg-red-50 text-red-700 rounded">
        未割当の栽培株が
        {{ $orderUnassignedCount }}株
        あります。
    </div>
@else
    <div class="mt-2 p-3 bg-green-50 text-green-700 rounded">
        すべての予約株が割り当て済みです。
    </div>
    <a
    href="{{ route('admin.cultivations.index') }}"
    class="inline-block mt-3 text-blue-600 hover:underline"
>
    栽培割当画面へ
</a>
@endif

                        <p class="text-sm text-gray-600">
                            購入者:
                            {{ $order->user->name }}
                            （{{ $order->user->email }}）
                        </p>

                        <p class="text-sm text-gray-600">
                            イベント:
                            {{ $order->event->name }}
                        </p>

                        <p class="text-sm text-gray-600">
                            注文日時:
                            {{ $order->ordered_at }}
                        </p>

                        <form
    method="POST"
    action="{{ route('admin.orders.update-status', $order) }}"
    class="mt-3 flex items-center gap-3"
>
    @csrf
    @method('PATCH')

    <label for="status-{{ $order->id }}" class="text-sm">
        状態:
    </label>

    <select
        id="status-{{ $order->id }}"
        name="status"
        class="rounded border-gray-300"
    >
        @foreach ([
            'reserved' => '予約済み',
            'confirmed' => '確認済み',
            'ready' => '受け渡し準備完了',
            'completed' => '完了',
            'cancelled' => 'キャンセル',
        ] as $value => $label)
            <option
                value="{{ $value }}"
                @selected($order->status === $value)
            >
                {{ $label }}
            </option>
        @endforeach
    </select>

    <button
        type="submit"
        class="px-3 py-2 bg-gray-800 text-white rounded"
    >
        更新
    </button>
</form>
                    </div>

                    @foreach ($order->items as $item)
    @php
    $assignedCount = $item->cultivations->count();

    $unassignedCount = max(
        $item->quantity - $assignedCount,
        0
    );

    $readyCount = $item->cultivations
        ->where('status', 'ready')
        ->count();

    $allReady =
        $unassignedCount === 0
        && $assignedCount > 0
        && $readyCount === $assignedCount;
@endphp

    <div class="mt-4 pt-4 border-t">
                            <p class="font-semibold">
                                {{ $item->eventProduct->product->name }}
                            </p>

                            <p class="text-sm">
                                数量:
                                {{ $item->quantity }}
                            </p>
                           <p class="text-sm">
    予約数:
    {{ $item->quantity }}株
</p>

<p class="text-sm">
    割当済み:
    {{ $assignedCount }}株
</p>

@if ($unassignedCount > 0)
    <p class="text-sm font-semibold text-red-600">
        未割当:
        {{ $unassignedCount }}株
    </p>
@else
    <p class="text-sm text-green-700">
        未割当: 0株
    </p>
@endif
@if ($allReady)
    <div class="mt-2 p-3 bg-green-50 text-green-700 rounded">
        この商品の栽培株はすべて受け渡し準備完了です。
    </div>
@elseif ($unassignedCount > 0)
    <div class="mt-2 p-3 bg-red-50 text-red-700 rounded">
        未割当の栽培株があります。
    </div>
@else
    <div class="mt-2 p-3 bg-yellow-50 text-yellow-700 rounded">
        まだ育成中の栽培株があります。
    </div>
@endif

                            <p class="text-sm">
                                単価:
                                ¥{{ number_format($item->unit_price) }}
                            </p>

                            <p class="text-sm">
                                小計:
                                ¥{{ number_format(
                                    $item->quantity * $item->unit_price
                                ) }}
                            </p>

                            @if ($item->cultivations->isNotEmpty())
                                <div class="mt-3">
                                    <p class="text-sm font-semibold">
                                        割り当て済み栽培株
                                    </p>

                                    @foreach ($item->cultivations as $cultivation)
                                        <div class="mt-2 pl-3 border-l">
                                            <p class="text-sm">
                                                状態:
                                                {{ $cultivation->status }}
                                            </p>

                                            @if ($cultivation->slot)
                                                <p class="text-sm">
                                                    Hexycle:
                                                    {{ $cultivation->slot->device->name }}
                                                </p>

                                                <p class="text-sm">
                                                    育成位置:
                                                    Level {{ $cultivation->slot->level }}
                                                    / {{ $cultivation->slot->position }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-gray-400">
                                    栽培株は未割当です。
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @empty
                <p>
                    注文はまだありません。
                </p>
            @endforelse

            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
</x-app-layout>