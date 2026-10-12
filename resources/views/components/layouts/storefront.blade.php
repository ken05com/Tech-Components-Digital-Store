<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Tech Components — Discover. Shop. Upgrade. Browse the latest tech gadgets, PC components, audio, storage and accessories.">

    <title>{{ ($pageTitle ?? '') ? $pageTitle.' · Tech Components' : 'Tech Components — Discover. Shop. Upgrade.' }}</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white bg-none font-sans text-ink antialiased selection:bg-brand/15 selection:text-brand">

    <a href="#storefront-main" class="skip-link">Skip to content</a>

    <x-storefront.header />

    <main id="storefront-main" class="min-h-screen bg-white pb-16">
        {{ $slot }}
    </main>

    <x-storefront.footer />

    @stack('scripts')
</body>
</html>