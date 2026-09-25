@extends('layouts.public')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">{{ $event->name }}</h1>

        <p class="mb-4">
            {{ $event->description }}
        </p>

        <div class="mb-6 text-sm text-gray-600">
            <p>
                開催期間:
                {{ $event->start_at }} 〜 {{ $event->end_at }}
            </p>

            <p>
                予約期間:
                {{ $event->reservation_start_at }} 〜 {{ $event->reservation_end_at }}
            </p>
        </div>

        <h2 class="text-xl font-semibold mb-3">販売予定の苗</h2>

        @forelse ($event->eventProducts as $eventProduct)
            @if ($eventProduct->is_available)
                <div class="mb-4 p-4 border rounded-lg">
                    <h3 class="font-semibold">
                        {{ $eventProduct->product->name }}
                    </h3>

                    <p class="text-sm text-gray-600">
                        {{ $eventProduct->product->description }}
                    </p>

                    <p class="mt-2">
                        価格: ¥{{ number_format($eventProduct->price) }}
                    </p>

                    <p>
                        在庫: {{ $eventProduct->stock }}
                    </p>
                </div>
            @endif
        @empty
            <p>現在、販売予定の商品はありません。</p>
        @endforelse
    </div>
@endsection
