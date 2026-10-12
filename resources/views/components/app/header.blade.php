@props([
    'title' => null,
    'active' => null,
    'term' => '',
])

@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 border-b border-line bg-white/85 backdrop-blur-xl">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">

        <button
            type="button"
            @click="sidebarOpen = true"
            class="rounded-xl p-2 text-muted transition hover:bg-line/40 hover:text-ink lg:hidden"
            aria-label="Open navigation"
        >
            <x-app.icon name="menu" />
        </button>

        {{-- Global search --}}
        @unless (request()->routeIs('search'))
            <form action="{{ route('search') }}" method="GET" role="search" class="relative hidden max-w-md flex-1 sm:block">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                    <x-app.icon name="search" />
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $term }}"
                    placeholder="Search products, categories…"
                    aria-label="Search the catalog"
                    class="h-10 w-full rounded-xl border border-line bg-panel/80 pl-10 pr-4 text-[13px] text-ink placeholder:text-muted transition focus:border-accent/50 focus:bg-panel focus:outline-none focus:ring-2 focus:ring-accent/20"
                >
            </form>
        @endunless

        <div class="ml-auto flex items-center gap-2 sm:gap-3">

            <a
                href="{{ route('home') }}"
                title="Open the public Tech Components storefront"
                class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-panel px-2.5 text-[13px] font-semibold text-ink transition hover:border-accent/40 hover:bg-accent/5 hover:text-accent sm:px-3.5"
            >
                <x-app.icon name="home" />
                <span class="hidden sm:inline">Storefront</span>
            </a>

            <span class="hidden h-6 w-px bg-line sm:block" aria-hidden="true"></span>

            @if ($user)
                <a href="{{ route('profile') }}" class="group flex items-center gap-2.5 rounded-xl p-1 pr-2 transition hover:bg-line/40">
                    <span class="relative grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-xl bg-accent/20 text-[12px] font-bold text-accent ring-1 ring-accent/25 transition group-hover:ring-accent/50">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-accent" aria-hidden="true">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    <span class="hidden text-left leading-tight md:block">
                        <span class="block text-[13px] font-semibold text-ink">{{ $user->name }}</span>
                    </span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-surface-strong px-3 text-[12.5px] font-semibold text-ink transition hover:border-rose-500/40 hover:text-rose-600"
                        aria-label="Sign out"
                    >
                        <span class="hidden sm:inline">Sign out</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</header>
