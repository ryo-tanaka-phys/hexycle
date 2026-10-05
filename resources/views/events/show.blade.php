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
@if ($event->programs->isNotEmpty())
    <section class="mt-10">
        <h2 class="text-2xl font-semibold mb-4">
            イベント企画
        </h2>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($event->programs as $program)
                <div class="p-6 bg-white rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold">
                        {{ $program->title }}
                    </h3>

                    @if ($program->description)
                        <p class="mt-3 text-gray-700">
                            {{ $program->description }}
                        </p>
                    @endif

                    @if ($program->start_at)
                        <p class="mt-4 text-sm text-gray-600">
                            開始:
                            {{ $program->start_at->format('Y年m月d日 H:i') }}
                        </p>
                    @endif

                    @if ($program->end_at)
                        <p class="text-sm text-gray-600">
                            終了:
                            {{ $program->end_at->format('Y年m月d日 H:i') }}
                        </p>
                    @endif

                    @if ($program->location)
                        <p class="text-sm text-gray-600">
                            場所:
                            {{ $program->location }}
                        </p>
                    @endif

                    @if ($program->capacity)
                        <p class="text-sm text-gray-600">
                            定員:
                            {{ $program->capacity }}人
                        </p>
                    @endif
                @if ($program->capacity !== null)
    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
        <p class="font-semibold">
            現在の混雑状況
        </p>

        <p class="mt-1">
            現在:
            {{ $program->inside_count }}
            /
            {{ $program->capacity }}
            人
        </p>

        <p class="text-sm text-gray-600">
            空き:
            {{ max(
                $program->capacity - $program->inside_count,
                0
            ) }}
            人
        </p>
    </div>
@endif
<a
    href="{{ route(
        'admission-reservations.create',
        $program
    ) }}"
    class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded"
>
    時間帯予約へ
</a>
                </div>
            @endforeach
        </div>
    </section>
@endif
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
