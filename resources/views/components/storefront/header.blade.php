@php
    $categoryPills = [
        ['label' => 'Computer Components', 'icon' => 'cpu', 'slug' => 'computer-components'],
        ['label' => 'Storage Devices', 'icon' => 'database', 'slug' => 'storage-devices'],
        ['label' => 'Computer Accessories', 'icon' => 'headphones', 'slug' => 'computer-accessories'],
        ['label' => 'Networking', 'icon' => 'router', 'slug' => 'networking-components'],
        ['label' => 'Power & Connection', 'icon' => 'usb', 'slug' => 'power-connection'],
    ];

    $mainLinks = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Shop', 'route' => 'products.index'],
    ];

    $cartCount = app(\App\Cart\CartService::class)->count();
@endphp

<header
    x-data="{ menuOpen: false, region: 'US' }"
    x-cloak
    @keydown.escape.window="menuOpen = false"
    class="sticky top-0 z-40 border-b border-line-soft bg-white/95 backdrop-blur"
>
    <div class="mx-auto w-full max-w-[1440px] px-4 sm:px-6 lg:px-10">

        {{-- Row 1: Brand, search, main nav, actions --------------------------- --}}
        <div class="flex h-16 w-full items-center justify-between gap-3 lg:gap-6">
            {{-- Left group: Mobile menu trigger & Brand --}}
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    x-on:click="menuOpen = !menuOpen"
                    :aria-expanded="menuOpen ? 'true' : 'false'"
                    aria-label="Toggle navigation menu"
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full text-ink transition hover:bg-surface lg:hidden"
                >
                    <template x-if="!menuOpen"><x-storefront.icon name="menu" class="h-5.5 w-5.5" /></template>
                    <template x-if="menuOpen"><x-storefront.icon name="close" class="h-5.5 w-5.5" /></template>
                </button>

                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Tech Components — home">
                    <img src="{{ asset('images/logo.png') }}" alt="Tech Components logo" class="h-9 w-9 object-contain rounded-xl">
                    <span class="leading-tight">
                        <span class="block text-[15px] font-extrabold tracking-tight text-ink sm:text-[16px]">Tech Components</span>
                        <span class="hidden text-[9.5px] font-semibold uppercase tracking-[0.18em] text-muted sm:block">Discover. Shop. Upgrade.</span>
                    </span>
                </a>
            </div>

            {{-- Main nav links (Desktop) --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main navigation">
                @foreach ($mainLinks as $link)
                    @php
                        $isActive = request()->routeIs($link['route']);
                    @endphp
                    <a
                        href="{{ route($link['route']) }}"
                        class="relative px-3.5 py-2 text-[13.5px] font-bold transition {{ $isActive ? 'text-brand' : 'text-ink hover:text-brand' }}"
                    >
                        {{ $link['label'] }}
                        @if ($isActive)
                            <span class="absolute inset-x-3.5 bottom-1 h-0.5 rounded-full bg-brand"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Search bar --}}
            <form action="{{ route('search') }}" method="get" class="relative hidden w-full max-w-xl md:block lg:flex-1 lg:px-6">
                <label for="store-search" class="sr-only">Search products</label>
                <input
                    id="store-search"
                    name="q"
                    type="search"
                    autocomplete="off"
                    placeholder="Search for tech gadgets, ideas and more"
                    class="h-10 w-full rounded-full border border-line-soft bg-surface pr-12 pl-5 text-[13px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                >
                <button
                    type="submit"
                    class="absolute inset-y-0 right-1 lg:right-7 my-1 grid w-10 place-items-center rounded-full text-ink transition hover:bg-brand hover:text-white"
                    aria-label="Submit search"
                >
                    <x-storefront.icon name="search" class="h-4.5 w-4.5" />
                </button>
            </form>

            {{-- Actions --}}
            <div class="flex items-center gap-1">
                {{-- Account --}}
                @auth
                    <a href="{{ route('profile') }}" class="grid h-10 w-10 place-items-center rounded-full text-ink transition hover:bg-surface hover:text-brand" aria-label="Your account">
                        <x-storefront.icon name="user" class="h-5 w-5" />
                    </a>
                @else
                    <a href="{{ route('login') }}" class="grid h-10 w-10 place-items-center rounded-full text-ink transition hover:bg-surface hover:text-brand" aria-label="Sign in">
                        <x-storefront.icon name="user" class="h-5 w-5" />
                    </a>
                @endauth

                {{-- Wishlist --}}
                <button
                    type="button"
                    class="relative grid h-10 w-10 place-items-center rounded-full text-ink transition hover:bg-surface hover:text-brand"
                    aria-label="Saved items"
                    x-on:click="$dispatch('open-wishlist')"
                >
                    <x-storefront.icon name="heart" class="h-5 w-5" />
                    <span
                        x-cloak
                        x-show="$store.storefront.count > 0"
                        x-text="$store.storefront.count"
                        class="absolute right-0.5 top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-brand px-1 text-[9px] font-bold text-white"
                    ></span>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full text-ink transition hover:bg-surface hover:text-brand" aria-label="Your cart">
                    <x-storefront.icon name="cart" class="h-5 w-5" />
                    @if ($cartCount > 0)
                        <span class="absolute right-0.5 top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-brand px-1 text-[9px] font-bold text-white">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Row 2: Stretched & Balanced Category pills and Region Selector (Desktop/Tablet) --}}
        <div class="hidden h-12 w-full items-center justify-between border-t border-line-soft md:flex">
            <nav class="flex flex-1 items-center justify-between gap-2 overflow-x-auto py-1 no-scrollbar" aria-label="Product categories">
                @foreach ($categoryPills as $pill)
                    @php
                        $isCatActive = request()->routeIs('categories.show') && request()->route('category') === $pill['slug'];
                    @endphp
                    <a
                        href="{{ route('categories.show', $pill['slug']) }}"
                        class="inline-flex items-center rounded-full px-4 py-1.5 text-[12.5px] font-bold transition {{ $isCatActive ? 'bg-brand text-white shadow-sm' : 'text-ink hover:bg-surface hover:text-brand' }}"
                    >
                        {{ $pill['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Mobile Menu Drawer ------------------------------------------------ --}}
        <div
            x-cloak
            x-show="menuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="border-t border-line-soft py-4 lg:hidden"
        >
            {{-- Mobile Search --}}
            <form action="{{ route('search') }}" method="get" class="relative mb-4 md:hidden">
                <label for="mobile-search" class="sr-only">Search products</label>
                <input
                    id="mobile-search"
                    name="q"
                    type="search"
                    placeholder="Search for tech gadgets, ideas and more"
                    class="h-10 w-full rounded-full border border-line-soft bg-surface pr-12 pl-5 text-[13px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                >
                <button type="submit" class="absolute inset-y-0 right-1 my-1 grid w-10 place-items-center rounded-full text-ink transition hover:bg-brand hover:text-white" aria-label="Submit search">
                    <x-storefront.icon name="search" class="h-4.5 w-4.5" />
                </button>
            </form>

            {{-- Mobile Main Links --}}
            <div class="grid grid-gap-1 space-y-1">
                @foreach ($mainLinks as $link)
                    <a href="{{ route($link['route']) }}" class="flex items-center justify-between rounded-xl px-4 py-2.5 text-[14px] font-bold text-ink transition hover:bg-surface hover:text-brand">
                        {{ $link['label'] }}
                        <x-storefront.icon name="arrow-right" class="h-4 w-4 text-muted" />
                    </a>
                @endforeach
            </div>

            <hr class="my-3 border-line-soft">

            {{-- Mobile Category Pills --}}
            <div class="grid grid-cols-2 gap-2">
                @foreach ($categoryPills as $pill)
                    <a href="{{ route('categories.show', $pill['slug']) }}" class="flex items-center justify-center rounded-xl bg-surface px-3 py-2 text-[12.5px] font-bold text-ink transition hover:bg-brand hover:text-white">
                        {{ $pill['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-line-soft pt-4 text-[12px] font-bold text-muted">
                <span>Region:</span>
                <div class="flex items-center gap-1">
                    <button type="button" x-on:click="region = 'US'" :class="region === 'US' ? 'bg-brand text-white' : 'bg-surface text-ink'" class="rounded px-2 py-1">US</button>
                    <button type="button" x-on:click="region = 'UK'" :class="region === 'UK' ? 'bg-brand text-white' : 'bg-surface text-ink'" class="rounded px-2 py-1">UK</button>
                    <button type="button" x-on:click="region = 'UAE'" :class="region === 'UAE' ? 'bg-brand text-white' : 'bg-surface text-ink'" class="rounded px-2 py-1">UAE</button>
                </div>
            </div>
        </div>

    </div>
</header>
