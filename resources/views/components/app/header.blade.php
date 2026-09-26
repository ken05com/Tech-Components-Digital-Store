@props([
    'title' => null,
    'active' => null,
    'term' => '',
])

<header class="sticky top-0 z-30 border-b border-line bg-shell/85 backdrop-blur-xl">
    <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">

        <button
            type="button"
            @click="sidebarOpen = true"
            class="rounded-xl p-2 text-slate-400 transition hover:bg-white/5 hover:text-white lg:hidden"
            aria-label="Open navigation"
        >
            <x-app.icon name="menu" />
        </button>

        {{-- Global search --}}
        <form action="{{ route('search') }}" method="GET" role="search" class="relative hidden max-w-md flex-1 sm:block">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
                <x-app.icon name="search" />
            </span>
            <input
                type="search"
                name="q"
                value="{{ $term }}"
                placeholder="Search products, categories…"
                aria-label="Search the catalog"
                class="h-10 w-full rounded-xl border border-line bg-panel/80 pl-10 pr-16 text-[13px] text-slate-200 placeholder:text-slate-600 transition focus:border-accent/50 focus:bg-panel focus:outline-none focus:ring-2 focus:ring-accent/20"
            >
            <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded border border-line bg-shell px-1.5 py-0.5 text-[10px] font-medium text-slate-500 md:block">/</kbd>
        </form>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">

            <a
                href="{{ route('products.create') }}"
                class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-3.5 text-[13px] font-semibold text-white shadow-[0_6px_20px_-6px_rgba(249,115,22,0.7)] transition hover:bg-accent-soft active:scale-[0.98] sm:px-4"
            >
                <x-app.icon name="plus" />
                <span class="hidden sm:inline">Add Product</span>
            </a>

            <span class="hidden h-6 w-px bg-line sm:block" aria-hidden="true"></span>

            <a href="{{ route('profile') }}" class="group flex items-center gap-2.5 rounded-xl p-1 pr-2 transition hover:bg-white/5">
                <span class="relative grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-xl bg-accent/20 text-[12px] font-bold text-accent ring-1 ring-accent/25 transition group-hover:ring-accent/50">
                    <img
                        src="{{ asset('images/profile-avatar.jpg') }}"
                        alt="{{ config('app.name') }} profile"
                        class="h-full w-full object-cover"
                        onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
                    >
                </span>
                <span class="hidden text-left leading-tight md:block">
                    <span class="block text-[13px] font-semibold text-slate-200">Shoby Keneth R. Daamo</span>
                    <span class="block text-[11px] text-slate-500">BSIT &ndash; 3A</span>
                </span>
            </a>
        </div>
    </div>
</header>
