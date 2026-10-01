<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                管理者向け商品一覧
            </h2>

            <a
                href="{{ route('admin.products.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded"
            >
                商品を追加
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($products as $product)
                <div class="mb-4 p-6 bg-white shadow-sm rounded-lg">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-lg font-semibold">
                                {{ $product->name }}
                            </h3>

                            @if ($product->variety)
                                <p class="text-sm text-gray-600">
                                    品種: {{ $product->variety }}
                                </p>
                            @endif

                            @if ($product->description)
                                <p class="mt-2 text-gray-700">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <p class="mt-2 text-sm">
                                状態:
                                @if ($product->is_active)
                                    <span class="font-semibold text-green-700">
                                        有効
                                    </span>
                                @else
                                    <span class="font-semibold text-gray-500">
                                        無効
                                    </span>
                                @endif
                            </p>
                        </div>

                        <a
                            href="{{ route('admin.products.edit', $product) }}"
                            class="px-3 py-2 bg-gray-800 text-white rounded"
                        >
                            編集
                        </a>
                    </div>
                </div>
            @empty
                <p>
                    商品はまだ登録されていません。
                </p>
            @endforelse

            <div class="mt-6">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-app-layout>