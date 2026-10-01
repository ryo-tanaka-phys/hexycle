@extends('layouts.public')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-semibold">
            入場予約確認
        </h1>

        @if (session('success'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('reservation'))
            <div class="mt-4 p-4 bg-red-100 text-red-800 rounded">
                {{ $errors->first('reservation') }}
            </div>
        @endif

        <div class="mt-6 p-6 bg-white rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold">
                {{ $reservation->eventProgram->title }}
            </h2>

            <p class="mt-4">
                予約時間:
                {{ $reservation->slot_start->format('Y年m月d日 H:i') }}
                ～
                {{ $reservation->slot_end->format('H:i') }}
            </p>

            <p class="mt-2">
                人数:
                {{ $reservation->party_size }}人
            </p>

            <p class="mt-2">
                名前:
                {{ $reservation->guest_name ?: '匿名' }}
            </p>

            <p class="mt-2">
                状態:
                {{ $reservation->status }}
            </p>

            <p class="mt-4 text-sm text-gray-600">
                予約コード:
                {{ $reservation->reservation_code }}
            </p>

            @if ($reservation->status === 'reserved')
                <form
                    method="POST"
                    action="{{ route(
                        'admission-reservations.cancel',
                        $reservation->reservation_code
                    ) }}"
                    class="mt-6"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded"
                    >
                        予約をキャンセル
                    </button>
                </form>
            @endif

            <a
                href="{{ route(
                    'admission-reservations.create',
                    $reservation->eventProgram
                ) }}"
                class="inline-block mt-6 text-blue-600 hover:underline"
            >
                時間帯一覧へ戻る
            </a>
        </div>
    </div>
@endsection