<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Hexycle' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

            <a href="/" class="text-xl font-bold">
                Hexycle
            </a>

            <nav class="flex gap-4">
    <a href="{{ route('home') }}"
       class="text-gray-700 hover:text-black">
        Hexycle
    </a>

    <a href="{{ route('events.show', 1) }}"
       class="text-gray-700 hover:text-black">
        文化祭
    </a>

    <a href="{{ route('products.index') }}"
       class="text-gray-700 hover:text-black">
        苗一覧
    </a>

    @auth
        <a href="{{ route('dashboard') }}"
           class="text-gray-700 hover:text-black">
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}"
           class="text-gray-700 hover:text-black">
            ログイン
        </a>

        <a href="{{ route('register') }}"
           class="text-gray-700 hover:text-black">
            登録
        </a>
    @endauth
</nav>

        </div>
    </header>

    <main class="max-w-7xl mx-auto">
        @yield('content')
    </main>
</body>
</html>