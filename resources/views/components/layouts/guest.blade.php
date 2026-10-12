<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Sign in' }} · {{ config('app.name', 'Tech Components') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-shell text-ink antialiased">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 flex items-center justify-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-accent/15 text-accent ring-1 ring-accent/30">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="12" rx="2" />
                        <path d="M8 20h8M12 16v4" stroke-linecap="round" />
                        <path d="M8 9.5h2.5M8 12h5" stroke-linecap="round" />
                    </svg>
                </span>
                <div class="leading-tight">
                    <p class="text-[14px] font-bold tracking-[0.14em] text-ink">TECH COMPONENTS</p>
                    <p class="text-[11px] text-muted">Inventory Catalog</p>
                </div>
            </div>

            {{ $slot }}

            <p class="mt-6 text-center text-[11px] text-muted">
                &copy; {{ now()->year }} Tech Components &mdash;
                <a href="{{ route('home') }}" class="font-semibold text-accent transition hover:text-accent-soft">Visit the Tech Components storefront</a>
            </p>
        </div>
    </main>
</body>
</html>
