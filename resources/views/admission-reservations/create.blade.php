@extends('layouts.public')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-semibold">
            {{ $eventProgram->title }} 入場予約
        </h1>

        <p class="mt-2 text-gray-600">
            {{ $eventProgram->location }}
        </p>

        @if (session('success'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 grid gap-4">
            @forelse ($slots as $slot)
                <div class="p-5 bg-white rounded-lg shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="font-semibold text-lg">
                                {{ $slot['start']->format('H:i') }}
                                –
                                {{ $slot['end']->format('H:i') }}
                            </p>

                            @if ($slot['remaining'] !== null)
                                <p class="text-sm text-gray-600">
                                    予約済み:
                                    {{ $slot['reserved_count'] }}人
                                    /
                                    定員:
                                    {{ $eventProgram->capacity }}人
                                </p>

                                <p class="text-sm">
                                    残席:
                                    {{ $slot['remaining'] }}人
                                </p>
                            @endif
                        </div>

                        @if (
                            $slot['remaining'] === null
                            || $slot['remaining'] > 0
                        )
                            <form
                                method="POST"
                                action="{{ route(
                                    'admission-reservations.store',
                                    $eventProgram
                                ) }}"
                                class="flex items-end gap-3"
                            >
                                @csrf

                                <input
                                    type="hidden"
                                    name="slot_start"
                                    value="{{ $slot['start']->format('Y-m-d H:i:s') }}"
                                >

                                <div>
                                    <label class="block text-sm font-semibold">
                                        名前
                                    </label>

                                    <input
                                        type="text"
                                        name="guest_name"
                                        class="mt-1 rounded border-gray-300"
                                        placeholder="任意"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold">
                                        人数
                                    </label>

                                    <input
                                        type="number"
                                        name="party_size"
                                        value="1"
                                        min="1"
                                        max="{{ $slot['remaining'] ?? 10 }}"
                                        class="mt-1 w-20 rounded border-gray-300"
                                        required
                                    >
                                </div>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded"
                                >
                                    予約
                                </button>
                            </form>
                        @else
                            <span class="font-semibold text-red-600">
                                満席
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p>
                    現在、予約可能な時間帯はありません。
                </p>
            @endforelse
        </div>

        @if ($errors->any())
            <div class="mt-6 p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection