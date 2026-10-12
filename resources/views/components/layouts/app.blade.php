<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Tech Components — dashboard and inventory catalog for computer hardware, with the public storefront.">

    <title>{{ $pageTitle ?? 'Dashboard' }} · {{ config('app.name', 'Tech Components') }}</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-shell text-ink antialiased selection:bg-accent/16 selection:text-accent">

    <a href="#main-content" class="skip-link">Skip to content</a>

    <div x-data="appShell()" class="min-h-screen">

        {{-- Mobile / tablet sidebar backdrop --}}
        <div
            x-cloak
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden"
            aria-hidden="true"
        ></div>

        @if (($active ?? null) !== 'search')
            <x-app.sidebar :active="$active ?? null" />
        @endif

        <div class="flex min-h-screen flex-col lg:ml-[264px] lg:w-[calc(100%-264px)]">

            <x-app.header />

            <main id="main-content" class="flex-1 px-4 pb-10 pt-5 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-[1400px] space-y-5">
                    <x-app.flash />
                    <x-app.success-dialog />
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-line/70 px-4 py-4 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-[1400px] text-xs text-muted">
                    <p>&copy; {{ now()->year }} Tech Components &mdash; Inventory Catalog.</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
