<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            管理者向け栽培株一覧
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @forelse ($cultivations as $cultivation)
                <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">

                    <div class="mb-4">
                        <p class="font-semibold text-lg">
                            栽培株 #{{ $cultivation->id }}
                        </p>

                        <p>
                            商品:
                            {{ $cultivation->product->name }}
                        </p>

                        <form
    method="POST"
    action="{{ route('admin.cultivations.update-status', $cultivation) }}"
    class="mt-3 flex items-center gap-3"
>
    @csrf
    @method('PATCH')

    <label for="status-{{ $cultivation->id }}" class="text-sm">
        状態:
    </label>

    <select
        id="status-{{ $cultivation->id }}"
        name="status"
        class="rounded border-gray-300"
    >
        @foreach ([
            'growing' => '育成中',
            'ready' => '受け渡し準備完了',
            'harvested' => '収穫済み',
            'failed' => '育成失敗',
        ] as $value => $label)
            <option
                value="{{ $value }}"
                @selected($cultivation->status === $value)
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

                        @if ($cultivation->planted_at)
                            <p>
                                播種日:
                                {{ $cultivation->planted_at }}
                            </p>
                        @endif
                    </div>

                    <div class="pt-4 border-t">
                        <p class="font-semibold">
                            育成位置
                        </p>

                        @if ($cultivation->slot)
                            <p>
                                Hexycle:
                                {{ $cultivation->slot->device->name }}
                            </p>

                            <p>
                                Level {{ $cultivation->slot->level }}
                                / {{ $cultivation->slot->position }}
                            </p>
                        @else
                            <p class="text-gray-400">
                                スロット未割当
                            </p>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t">
    <p class="font-semibold">
        予約情報
    </p>

    @if ($cultivation->orderItem?->order?->user)
        <p>
            予約者:
            {{ $cultivation->orderItem->order->user->name }}
        </p>

        <p>
            メール:
            {{ $cultivation->orderItem->order->user->email }}
        </p>

        <p>
            注文 #{{ $cultivation->orderItem->order->id }}
        </p>
    @else
        <p class="text-gray-400">
            予約未割当
        </p>
    @endif

    <form
        method="POST"
        action="{{ route('admin.cultivations.assign', $cultivation) }}"
        class="mt-4"
    >
        @csrf
        @method('PATCH')

        <label
            for="order-item-{{ $cultivation->id }}"
            class="block text-sm font-semibold mb-2"
        >
            注文への割り当て
        </label>

        <select
            id="order-item-{{ $cultivation->id }}"
            name="order_item_id"
            class="rounded border-gray-300 w-full"
        >
            <option value="">
                未割当
            </option>

            @foreach ($orderItems as $orderItem)
                @if ($orderItem->eventProduct->product_id === $cultivation->product_id)
                    <option
                        value="{{ $orderItem->id }}"
                        @selected($cultivation->order_item_id === $orderItem->id)
                    >
                        注文 #{{ $orderItem->order->id }}
                        /
                        {{ $orderItem->order->user->name }}
                        /
                        {{ $orderItem->eventProduct->product->name }}
                        /
                        数量 {{ $orderItem->quantity }}
                    </option>
                @endif
            @endforeach
        </select>

        @error('order_item_id')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

        <button
            type="submit"
            class="mt-3 px-3 py-2 bg-gray-800 text-white rounded"
        >
            割り当て更新
        </button>
    </form>
</div>

@empty
    <p>
        栽培株はまだありません。
    </p>
@endforelse

<div class="mt-6">
    {{ $cultivations->links() }}
</div>

        </div>
    </div>
</x-app-layout>