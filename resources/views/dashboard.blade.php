<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hexycle ダッシュボード
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (auth()->user()->isAdmin())

                <div class="mb-6">
                    <h3 class="text-xl font-semibold">
                        管理メニュー
                    </h3>

                    <p class="mt-1 text-sm text-gray-600">
                        Hexycle の運用機能へ移動できます。
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="block p-6 bg-white shadow-sm rounded-lg hover:shadow-md"
                    >
                        <h4 class="text-lg font-semibold">
                            注文管理
                        </h4>

                        <p class="mt-2 text-sm text-gray-600">
                            商品予約、注文状態、栽培株の割当状況を確認します。
                        </p>
                    </a>

                    <a
                        href="{{ route('admin.products.index') }}"
                        class="block p-6 bg-white shadow-sm rounded-lg hover:shadow-md"
                    >
                        <h4 class="text-lg font-semibold">
                            商品管理
                        </h4>

                        <p class="mt-2 text-sm text-gray-600">
                            販売商品の登録・編集・公開状態を管理します。
                        </p>
                    </a>

                    <a
                        href="{{ route('admin.cultivations.index') }}"
                        class="block p-6 bg-white shadow-sm rounded-lg hover:shadow-md"
                    >
                        <h4 class="text-lg font-semibold">
                            栽培管理
                        </h4>

                        <p class="mt-2 text-sm text-gray-600">
                            栽培株の状態、育成位置、注文への割当を管理します。
                        </p>
                    </a>

                    <a
                        href="{{ route('devices.index') }}"
                        class="block p-6 bg-white shadow-sm rounded-lg hover:shadow-md"
                    >
                        <h4 class="text-lg font-semibold">
                            デバイス管理
                        </h4>

                        <p class="mt-2 text-sm text-gray-600">
                            Hexycle本体、スロット、センサーログを確認します。
                        </p>
                    </a>

                </div>

            @else

                <div class="p-6 bg-white shadow-sm rounded-lg">
                    <h3 class="text-lg font-semibold">
                        マイページ
                    </h3>

                    <div class="mt-4 flex gap-4">
                        <a
                            href="{{ route('orders.index') }}"
                            class="text-blue-600 hover:underline"
                        >
                            自分の予約を見る
                        </a>

                        <a
                            href="{{ route('products.index') }}"
                            class="text-blue-600 hover:underline"
                        >
                            商品を見る
                        </a>
                    </div>
                </div>

            @endif
<div class="mt-8">
    <h3 class="text-xl font-semibold">
        イベント運営
    </h3>

    <p class="mt-1 text-sm text-gray-600">
        公開中のイベント企画の入退場管理へ移動できます。
    </p>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($eventPrograms as $program)
            <a
                href="{{ route('admin.visits.index', $program) }}"
                class="block p-6 bg-white shadow-sm rounded-lg hover:shadow-md"
            >
                <h4 class="text-lg font-semibold">
                    {{ $program->title }}
                </h4>

                <p class="mt-2 text-sm text-gray-600">
                    @if ($program->start_at)
                        {{ $program->start_at->format('Y年m月d日 H:i') }}
                    @endif

                    @if ($program->location)
                        / {{ $program->location }}
                    @endif
                </p>

                <p class="mt-3 text-blue-600">
                    入退場管理を開く
                </p>
            </a>
        @empty
            <div class="p-6 bg-white shadow-sm rounded-lg">
                <p class="text-gray-500">
                    公開中のイベント企画はありません。
                </p>
            </div>
        @endforelse
    </div>
</div>
        </div>
        
    </div>
    
</x-app-layout>