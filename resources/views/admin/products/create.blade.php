<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            商品を追加
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-sm rounded-lg">

                <form
                    method="POST"
                    action="{{ route('admin.products.store') }}"
                >
                    @csrf

                    <div>
                        <label
                            for="name"
                            class="block text-sm font-semibold"
                        >
                            商品名
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded border-gray-300"
                            required
                        >

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label
                            for="variety"
                            class="block text-sm font-semibold"
                        >
                            品種
                        </label>

                        <input
                            id="variety"
                            name="variety"
                            type="text"
                            value="{{ old('variety') }}"
                            class="mt-1 block w-full rounded border-gray-300"
                        >

                        @error('variety')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label
                            for="description"
                            class="block text-sm font-semibold"
                        >
                            説明
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="mt-1 block w-full rounded border-gray-300"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold">
                            状態
                        </label>

                        <select
                            name="is_active"
                            class="mt-1 block w-full rounded border-gray-300"
                        >
                            <option value="1" @selected(old('is_active', '1') === '1')>
                                有効
                            </option>

                            <option value="0" @selected(old('is_active') === '0')>
                                無効
                            </option>
                        </select>

                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded"
                        >
                            登録
                        </button>

                        <a
                            href="{{ route('admin.products.index') }}"
                            class="px-4 py-2 bg-gray-200 rounded"
                        >
                            戻る
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>