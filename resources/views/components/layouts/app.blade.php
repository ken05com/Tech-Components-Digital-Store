<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Tech Components — dark themed dashboard and inventory catalog for computer components, storage devices, accessories, networking and power &amp; connection products.">

    <title>{{ $pageTitle ?? 'Dashboard' }} · {{ config('app.name', 'Tech Components') }}</title>

    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-shell text-slate-200 antialiased selection:bg-accent/30 selection:text-white">

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

        <x-app.sidebar :active="$active ?? null" />

        <div class="flex min-h-screen flex-col lg:pl-[264px]">

            <x-app.header />

            <main id="main-content" class="flex-1 px-4 pb-10 pt-5 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-[1400px] space-y-5">
                    <x-app.flash />
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t border-line/70 px-4 py-4 sm:px-6 lg:px-8">
                <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-1 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ now()->year }} Tech Components — Inventory Catalog.</p>
                    <p>Catalog source: <span class="text-slate-400">the subtopis and samples.docx</span> · 25 products · 5 subtopics</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
