<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Hexycle' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    @php
    $currentEvent = \App\Models\Event::where('status', 'published')
        ->orderBy('start_at')
        ->first();
@endphp
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <a href="/" class="text-xl font-bold">
                Hexycle
            </a>

            <nav class="flex items-center gap-4">
    <a
        href="{{ route('home') }}"
        class="text-gray-700 hover:text-black"
    >
        ホーム
    </a>

    @if ($currentEvent)
    <a
        href="{{ route('events.show', $currentEvent) }}"
        class="text-gray-700 hover:text-black"
    >
        文化祭
    </a>
@endif

    <a
        href="{{ route('products.index') }}"
        class="text-gray-700 hover:text-black"
    >
        苗一覧
    </a>

    @auth
        <a
            href="{{ route('orders.index') }}"
            class="text-gray-700 hover:text-black"
        >
            自分の予約
        </a>

        <a
            href="{{ route('dashboard') }}"
            class="text-gray-700 hover:text-black"
        >
            @if (auth()->user()->isAdmin())
                管理画面
            @else
                マイページ
            @endif
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="text-gray-700 hover:text-black"
        >
            ログイン
        </a>

        <a
            href="{{ route('register') }}"
            class="text-gray-700 hover:text-black"
        >
            新規登録
        </a>
    @endauth
</nav>

        </div>
    </header>

    <main class="max-w-7xl mx-auto">
        @yield('content')
    </main>
</body>
@php
    $currentEvent = \App\Models\Event::where('status', 'published')
        ->orderBy('start_at')
        ->first();
@endphp
</html>