<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('予約履歴') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @forelse ($orders as $order)
                <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">

                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ $order->event->name }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            予約日時:
                            {{ $order->ordered_at }}
                        </p>

                        <p class="mt-1">
                            状態:
                            {{ $order->status }}
                        </p>
                    </div>

                    @foreach ($order->items as $item)
                        <div class="border-t py-3">
                            <p class="font-semibold">
                                {{ $item->eventProduct->product->name }}
                            </p>

                            <p>
                                数量: {{ $item->quantity }}
                            </p>

                            <p>
                                単価: ¥{{ number_format($item->unit_price) }}
                            </p>

                            <p>
                                小計:
                                ¥{{ number_format($item->unit_price * $item->quantity) }}
                            </p>
                            @if ($item->cultivations->isNotEmpty())
    <div class="mt-3 pt-3 border-t">
        <p class="font-semibold">
            育成状況
        </p>

        @foreach ($item->cultivations as $cultivation)
            <div class="mt-2 text-sm">
                <p>
                    状態: {{ $cultivation->status }}
                </p>

                @if ($cultivation->slot)
                    <p>
                        Hexycle:
                        {{ $cultivation->slot->device->name }}
                    </p>

                    <p>
                        育成位置:
                        Level {{ $cultivation->slot->level }}
                        / {{ $cultivation->slot->position }}
                    </p>
                @endif

                @if ($cultivation->planted_at)
                    <p>
                        播種日: {{ $cultivation->planted_at }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
@else
    <p class="mt-3 text-sm text-gray-400">
        育成株はまだ割り当てられていません。
    </p>
@endif
                        </div>
                    @endforeach

                </div>
            @empty
                <div class="p-6 bg-white shadow-sm rounded-lg">
                    <p>まだ予約はありません。</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
</x-app-layout>