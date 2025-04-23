<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'POS System') }}</title>

    <!-- Fonts -->
    @if(file_exists(public_path('css/fonts.css')))
        <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Styles -->
{{--    <style>--}}
{{--        [x-cloak] { display: none !important; }--}}

{{--        html, body {--}}
{{--            height: 100%;--}}
{{--            overscroll-behavior: none;--}}
{{--        }--}}

{{--        .scrollbar-hide {--}}
{{--            -ms-overflow-style: none;--}}
{{--            scrollbar-width: none;--}}
{{--        }--}}
{{--        .scrollbar-hide::-webkit-scrollbar {--}}
{{--            display: none;--}}
{{--        }--}}
{{--    </style>--}}
</head>
<body class="font-sans antialiased bg-gray-100 h-full">
    <div class="h-full flex flex-col min-h-screen">
        <x-navigations.navigation />

        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

    @stack('scripts')

    <script>
        // Add global hook to properly cleanup Alpine components before Livewire replaces them
        document.addEventListener('livewire:load', function() {
            // Create a backup of the cart before Livewire updates
            let cartBackup = null;

            Livewire.hook('message.sent', () => {
                try {
                    cartBackup = JSON.parse(localStorage.getItem('pos_cart'));
                } catch (e) {
                    cartBackup = null;
                }
            });

            Livewire.hook('message.processed', () => {
                try {
                    if (cartBackup) {
                        localStorage.setItem('pos_cart', JSON.stringify(cartBackup));
                    }
                } catch (e) {
                    console.error('Error restoring cart backup:', e);
                }
            });

            // Fix the element removal hook
            Livewire.hook('element.removed', (el) => {
                // Cleanup Alpine components to prevent memory leaks
                if (window.Alpine) {
                    window.Alpine.closestRoot(el)?.cleanup?.();
                }
            }, 0);
        });
    </script>
</body>
</html>
