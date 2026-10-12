@props([
    'active' => null,
])

@php
    $products = app(\App\Catalog\ProductRepository::class);
    $categories = app(\App\Catalog\CategoryRepository::class);
    $catalogTotal = $products->count();
    $deletedTotal = auth()->user()?->can('view-deleted') ? $products->countTrashed() : null;
    $user = auth()->user();
    $isUserOnly = $user === null || $user->is(\App\Auth\Role::User);

    $nav = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid', 'match' => 'dashboard'],
    ];

    foreach ($categories->ordered() as $record) {
        $nav[] = [
            'route' => 'categories.overview',
            'params' => ['category' => $record->slug()],
            'label' => $record->label(),
            'icon' => $record->slug(),
            'match' => 'category-'.$record->slug(),
        ];
    }

    $management = [];

    if ($user?->is(\App\Auth\Role::SuperAdmin)) {
        $management[] = ['route' => 'inventory.index', 'label' => 'Inventory', 'icon' => 'layers'];
        $management[] = ['route' => 'sales.index', 'label' => 'Sales', 'icon' => 'tag'];
        $management[] = ['route' => 'categories.index', 'label' => 'Categories', 'icon' => 'layers'];
        $management[] = ['route' => 'users.index', 'label' => 'Users & Roles', 'icon' => 'user'];
    }

    $utilities = [];

    if ($user?->can('view-deleted')) {
        $utilities[] = ['route' => 'deleted-items.index', 'label' => 'Deleted Items', 'icon' => 'trash'];
    }

    $utilities[] = ['route' => 'profile', 'label' => 'Profile', 'icon' => 'user'];
    $utilities[] = ['route' => 'about', 'label' => 'About', 'icon' => 'info'];
@endphp

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-[264px] shrink-0 flex-col border-r border-line bg-sidebar shadow-[6px_0_24px_-18px_rgba(21,21,21,0.35)] transition-transform duration-300 ease-out lg:translate-x-0"
    aria-label="Main navigation"
>
    {{-- Brand --}}
    <a href="{{ route('home') }}" title="Open the public storefront" class="flex h-16 shrink-0 items-center gap-3 border-b border-line px-5 transition hover:bg-line/30">
        <img src="{{ asset('images/logo.png') }}" alt="Tech Components logo" class="h-9 w-9 object-contain rounded-xl">
        <span class="min-w-0 leading-tight">
            <span class="block text-[15px] font-extrabold tracking-tight text-ink sm:text-[16px]">Tech Components</span>
            <span class="block truncate text-[11px] text-muted">Storefront</span>
        </span>
        <!-- <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 shrink-0 text-muted" aria-hidden="true">
            <path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
        </svg> -->
    </a>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @if ($isUserOnly)
            {{-- User Account Navigation --}}
            <div class="space-y-4">
                {{-- User Snippet --}}
                <a href="{{ route('profile') }}" class="group flex items-center gap-3 rounded-xl border border-line bg-panel p-3 transition hover:border-accent/40 hover:bg-accent/5">
                    <span class="relative grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-xl bg-accent/20 text-[12px] font-bold text-accent ring-1 ring-accent/25 transition group-hover:ring-accent/50">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-accent" aria-hidden="true">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-semibold text-ink group-hover:text-accent">{{ $user?->name }}</p>
                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-muted transition">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-2.5 w-2.5" aria-hidden="true">
                                <path d="M4 20h4l10-10a2.83 2.83 0 1 0-4-4L4 16v4Z" stroke-linejoin="round"/>
                            </svg>
                            Edit Profile
                        </span>
                    </div>
                </a>

                <hr class="border-line">

                {{-- Other Top-Level Links --}}
                <ul class="space-y-1 text-[12.5px] font-medium">
                    <li>
                        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-muted transition hover:bg-line/40 hover:text-brand">
                            <span class="text-muted"><x-app.icon name="tag" /></span>
                            <span>My Purchase</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-muted transition hover:bg-line/40 hover:text-brand">
                            <span class="text-muted"><x-app.icon name="info" /></span>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('vouchers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-muted transition hover:bg-line/40 hover:text-brand">
                            <span class="text-muted"><x-app.icon name="layers" /></span>
                            <span>My Vouchers</span>
                        </a>
                    </li>
                </ul>
            </div>
        @else
            {{-- Admin / SuperAdmin Overview & Management Nav --}}
            <div>
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-muted">Overview</p>
                <ul class="space-y-1">
                    @foreach ($nav as $item)
                        @php 
                            $isActive = $active === $item['match']; 
                            $isHiddenWhenActive = $item['match'] !== 'dashboard' && $isActive;
                        @endphp
                        @unless ($isHiddenWhenActive)
                            <li>
                                <a
                                    href="{{ route($item['route'], $item['params'] ?? []) }}"
                                    @if ($isActive) aria-current="page" @endif
                                    class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-brand/10 text-brand shadow-[inset_0_0_0_1px_rgba(201,0,22,0.3)]' : 'text-muted hover:bg-line/40 hover:text-brand' }}"
                                >
                                    <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-accent transition-all duration-200 {{ $isActive ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50' }}"></span>
                                    <span class="{{ $isActive ? 'text-accent' : 'text-muted group-hover:text-ink' }} transition-colors duration-200">
                                        <x-app.icon :name="$item['icon']" />
                                    </span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endunless
                    @endforeach
                </ul>

                @if ($user?->can('manage-categories'))
                    <a
                        href="{{ route('categories.create') }}"
                        class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-accent px-3 py-2.5 text-[13px] font-semibold text-white shadow-[0_6px_20px_-6px_rgba(201,0,22,0.7)] transition hover:bg-accent-soft active:scale-[0.98]"
                    >
                        <x-app.icon name="plus" />
                        Add New Collection
                    </a>
                @endif
            </div>

            @if (count($management) > 0)
                <div>
                    <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-muted">Management</p>
                    <ul class="space-y-1">
                        @foreach ($management as $item)
                            @php $isActive = $active === $item['route']; @endphp
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    @if ($isActive) aria-current="page" @endif
                                    class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-brand/10 text-brand shadow-[inset_0_0_0_1px_rgba(201,0,22,0.3)]' : 'text-muted hover:bg-line/40 hover:text-brand' }}"
                                >
                                    <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-accent transition-all duration-200 {{ $isActive ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50' }}"></span>
                                    <span class="{{ $isActive ? 'text-accent' : 'text-muted group-hover:text-ink' }} transition-colors duration-200">
                                        <x-app.icon :name="$item['icon']" />
                                    </span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        @if (count($utilities) > 0)
            <div>
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-muted">Utilities</p>
                <ul class="space-y-1">
                    @foreach ($utilities as $item)
                        @php $isActive = $active === $item['route']; @endphp
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                @if ($isActive) aria-current="page" @endif
                                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-brand/10 text-brand shadow-[inset_0_0_0_1px_rgba(201,0,22,0.3)]' : 'text-muted hover:bg-line/40 hover:text-brand' }}"
                            >
                                <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-accent transition-all duration-200 {{ $isActive ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50' }}"></span>
                                <span class="{{ $isActive ? 'text-accent' : 'text-muted group-hover:text-ink' }} transition-colors duration-200">
                                    <x-app.icon :name="$item['icon']" />
                                </span>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </nav>

</aside>
