<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            入退場管理
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

          

            @if ($errors->has('visit'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ $errors->first('visit') }}
                </div>
            @endif

            <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
                <h3 class="text-xl font-semibold">
                    {{ $eventProgram->title }}
                </h3>

                <p class="mt-2">
                    場所:
                    {{ $eventProgram->location ?? '未設定' }}
                </p>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">
                            現在滞在
                        </p>

                        <p class="text-2xl font-semibold">
                            {{ $insideCount }}人
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            定員
                        </p>

                        <p class="text-2xl font-semibold">
                            {{ $eventProgram->capacity ?? '未設定' }}
                            @if ($eventProgram->capacity !== null)
                                人
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            空き
                        </p>

                        <p class="text-2xl font-semibold">
                            @if ($remainingCapacity !== null)
                                {{ $remainingCapacity }}人
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
                <h3 class="text-lg font-semibold mb-4">
                    入場登録
                </h3>

                <form
                    method="POST"
                    action="{{ route('admin.visits.store', $eventProgram) }}"
                >
                    @csrf

                    <div>
                        <label
                            for="guest_name"
                            class="block text-sm font-semibold"
                        >
                            来場者名
                        </label>

                        <input
                            id="guest_name"
                            name="guest_name"
                            type="text"
                            value="{{ old('guest_name') }}"
                            class="mt-1 block w-full rounded border-gray-300"
                            placeholder="匿名の場合は空欄でも可"
                        >

                        @error('guest_name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
<div class="mt-4">
    <label for="party_size" class="block text-sm font-medium text-gray-700">
        人数
    </label>

    <input
        type="number"
        id="party_size"
        name="party_size"
        value="{{ old('party_size', 1) }}"
        min="1"
        max="10"
        required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
    >

    @error('party_size')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
                    <button
                        type="submit"
                        class="mt-4 px-4 py-2 bg-gray-800 text-white rounded"
                    >
                        入場登録
                    </button>
                </form>
            </div>

            <div class="p-6 bg-white shadow-sm rounded-lg">
                <h3 class="text-lg font-semibold mb-4">
                    来場履歴
                </h3>

                @forelse ($visits as $visit)
                    <div class="py-4 border-b">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold">
                                    {{ $visit->guest_name ?: '匿名来場者' }}
                                </p>
<p class="text-sm text-gray-600">
    人数:
    {{ $visit->party_size }}人
</p>
                                <p class="text-sm text-gray-600">
                                    入場:
                                    {{ $visit->entered_at }}
                                </p>

                                <p class="text-sm text-gray-600">
                                    退場:
                                    {{ $visit->exited_at ?? '滞在中' }}
                                </p>

                                <p class="text-sm">
                                    状態:
                                    {{ $visit->status }}
                                </p>
                            </div>
<div class="mb-6 p-6 bg-white shadow-sm rounded-lg">
    <h3 class="text-lg font-semibold mb-4">
        入場予約
    </h3>

    @if ($errors->has('reservation'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
            {{ $errors->first('reservation') }}
        </div>
    @endif

    @forelse ($reservations as $reservation)
        <div class="py-4 border-b">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <p class="font-semibold">
                        {{ $reservation->guest_name ?: '匿名予約' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        予約時間:
                        {{ $reservation->slot_start->format('Y-m-d H:i') }}
                        ～
                        {{ $reservation->slot_end->format('H:i') }}
                    </p>

                    <p class="text-sm text-gray-600">
                        人数:
                        {{ $reservation->party_size }}人
                    </p>

                    <p class="text-sm">
                        状態:
                        {{ $reservation->status }}
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        予約コード:
                        {{ $reservation->reservation_code }}
                    </p>
                </div>

                @if ($reservation->status === 'reserved')
                    <form
                        method="POST"
                        action="{{ route(
                            'admin.admission-reservations.check-in',
                            $reservation
                        ) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="px-3 py-2 bg-blue-600 text-white rounded"
                        >
                            チェックイン
                        </button>
                    </form>
                @else
                    <span class="text-sm text-gray-500">
                        チェックイン済み
                    </span>
                @endif

            </div>
        </div>
    @empty
        <p>
            入場予約はありません。
        </p>
    @endforelse
</div>
                            @if ($visit->status === 'inside')
                                <form
                                    method="POST"
                                    action="{{ route('admin.visits.exit', $visit) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="px-3 py-2 bg-gray-800 text-white rounded"
                                    >
                                        退場
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>
                        来場記録はまだありません。
                    </p>
                @endforelse

                <div class="mt-6">
                    {{ $visits->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>