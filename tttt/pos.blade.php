<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS System') }}</title>

    <!-- Fonts -->
    @if(file_exists(public_path('css/fonts.css')))
        <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        [x-cloak] { display: none !important; }

        html, body {
            height: 100%;
            overscroll-behavior: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>

    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 h-full overflow-hidden">
<div class="h-full flex flex-col">
    <main class="flex-1 overflow-hidden">
        @yield('content')
    </main>
</div>

@livewireScripts
</body>
</html>
