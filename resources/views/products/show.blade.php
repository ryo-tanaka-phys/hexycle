@extends('layouts.public')

@section('content')
    <div class="p-6 max-w-4xl mx-auto">

        <a href="{{ route('products.index') }}"
           class="text-green-700 hover:underline">
            ← 苗一覧に戻る
        </a>

        <div class="mt-6 bg-white border rounded-lg p-6">

            <h1 class="text-3xl font-bold mb-2">
                {{ $eventProduct->product->name }}
            </h1>

            @if ($eventProduct->product->variety)
                <p class="text-gray-500 mb-4">
                    品種: {{ $eventProduct->product->variety }}
                </p>
            @endif

            <p class="text-lg mb-6">
                {{ $eventProduct->product->description }}
            </p>

            <div class="space-y-2">
                <p>
                    価格:
                    <strong>
                        ¥{{ number_format($eventProduct->price) }}
                    </strong>
                </p>

                <p>
                    在庫: {{ $eventProduct->stock }}
                </p>

                @if ($eventProduct->reservation_limit)
                    <p>
                        1人あたり予約上限:
                        {{ $eventProduct->reservation_limit }}株
                    </p>
                @endif

                <p>
                    販売イベント:
                    <a href="{{ route('events.show', $eventProduct->event) }}"
                       class="text-green-700 hover:underline">
                        {{ $eventProduct->event->name }}
                    </a>
                </p>
            </div>

            @if (session('success'))
                <div class="mt-6 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @auth
                @if ($eventProduct->stock > 0 && $eventProduct->is_available)
                    <form action="{{ route('orders.store', $eventProduct) }}"
                          method="POST"
                          class="mt-6">
                        @csrf

                        <label for="quantity"
                               class="block mb-2 font-semibold">
                            予約数量
                        </label>

                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            max="{{ min(
                                $eventProduct->stock,
                                $eventProduct->reservation_limit ?? $eventProduct->stock
                            ) }}"
                            value="{{ old('quantity', 1) }}"
                            class="border rounded px-3 py-2 w-24"
                        >

                        @error('quantity')
                            <p class="text-red-600 mt-2">
                                {{ $message }}
                            </p>
                        @enderror

                        <button type="submit"
                                class="ml-3 px-5 py-3 bg-green-700 text-white rounded-lg hover:bg-green-800">
                            予約する
                        </button>
                    </form>
                @else
                    <p class="mt-6 text-red-600 font-semibold">
                        現在この苗は予約できません。
                    </p>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="inline-block mt-6 px-5 py-3 bg-green-700 text-white rounded-lg hover:bg-green-800">
                    ログインして予約
                </a>
            @endauth

        </div>
    </div>
@endsection