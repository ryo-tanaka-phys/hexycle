@extends('layouts.public')

@section('content')
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">苗一覧</h1>

        @forelse ($eventProducts as $eventProduct)
            <div class="mb-4 p-4 border rounded-lg bg-white">
                <h2 class="text-lg font-semibold">
                    {{ $eventProduct->product->name }}
                </h2>

                @if ($eventProduct->product->variety)
                    <p class="text-sm text-gray-500">
                        品種: {{ $eventProduct->product->variety }}
                    </p>
                @endif

                <p class="mt-2">
                    {{ $eventProduct->product->description }}
                </p>

                <p class="mt-2">
                    価格: ¥{{ number_format($eventProduct->price) }}
                </p>

                <p>
                    在庫: {{ $eventProduct->stock }}
                </p>

                <p class="text-sm text-gray-500">
                    販売イベント: {{ $eventProduct->event->name }}
                </p>

                <a href="{{ route('products.show', $eventProduct) }}"
                   class="inline-block mt-3 text-green-700 font-semibold hover:underline">
                    詳細を見る →
                </a>
            </div>
        @empty
            <p>現在販売中の苗はありません。</p>
        @endforelse
    </div>
@endsection