<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hexycle端末一覧
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @forelse ($devices as $device)
                <div class="mb-4 p-6 bg-white shadow-sm rounded-lg">
                    <h3 class="text-lg font-semibold">
                        {{ $device->name }}
                    </h3>

                    <p>
                        Serial: {{ $device->serial_number }}
                    </p>

                    <p>
                        Status: {{ $device->status }}
                    </p>

                    <p>
                        Slots: {{ $device->slots_count }}
                    </p>

                    <a href="{{ route('devices.show', $device) }}"
                       class="inline-block mt-3 text-green-700 font-semibold hover:underline">
                        詳細を見る →
                    </a>
                </div>
            @empty
                <p>登録されているHexycle端末はありません。</p>
            @endforelse

        </div>
    </div>
</x-app-layout>