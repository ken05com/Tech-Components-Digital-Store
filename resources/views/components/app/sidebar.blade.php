@props([
    'active' => null,
])

@php
    use App\Catalog\Category;

    $nav = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid', 'match' => 'dashboard'],
    ];

    foreach (Category::ordered() as $case) {
        $nav[] = [
            'route' => 'categories.'.$case->slug(),
            'label' => $case->label(),
            'icon' => $case->slug(),
            'match' => 'category-'.$case->slug(),
        ];
    }

    $utilities = [
        ['route' => 'search', 'label' => 'Search', 'icon' => 'search'],
        ['route' => 'settings', 'label' => 'Settings', 'icon' => 'settings'],
        ['route' => 'profile', 'label' => 'Profile', 'icon' => 'user'],
    ];
@endphp

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-[264px] shrink-0 flex-col border-r border-line bg-sidebar transition-transform duration-300 ease-out lg:translate-x-0"
    aria-label="Main navigation"
>
    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-line px-5">
        <span class="grid h-9 w-9 place-items-center rounded-xl bg-accent/15 text-accent ring-1 ring-accent/30">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                <rect x="3" y="4" width="18" height="12" rx="2" />
                <path d="M8 20h8M12 16v4" stroke-linecap="round" />
                <path d="M8 9.5h2.5M8 12h5" stroke-linecap="round" />
            </svg>
        </span>
        <div class="min-w-0 leading-tight">
            <p class="truncate text-[13px] font-bold tracking-[0.14em] text-white">TECH COMPONENTS</p>
            <p class="truncate text-[11px] text-slate-500">Inventory Catalog</p>
        </div>
        <button
            type="button"
            @click="sidebarOpen = false"
            class="ml-auto rounded-lg p-1.5 text-slate-500 transition hover:bg-white/5 hover:text-slate-200 lg:hidden"
            aria-label="Close navigation"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round" /></svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div>
            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-600">Overview</p>
            <ul class="space-y-1">
                @foreach ($nav as $item)
                    @php $isActive = $active === $item['match']; @endphp
                    <li>
                        <a
                            href="{{ route($item['route']) }}"
                            @if ($isActive) aria-current="page" @endif
                            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-accent/12 text-white shadow-[inset_0_0_0_1px_rgba(249,115,22,0.28)]' : 'text-slate-400 hover:bg-white/[0.045] hover:text-slate-100' }}"
                        >
                            <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-accent transition-all duration-200 {{ $isActive ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50' }}"></span>
                            <span class="{{ $isActive ? 'text-accent' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors duration-200">
                                <x-app.icon :name="$item['icon']" />
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-600">Utilities</p>
            <ul class="space-y-1">
                @foreach ($utilities as $item)
                    @php $isActive = $active === $item['route']; @endphp
                    <li>
                        <a
                            href="{{ route($item['route']) }}"
                            @if ($isActive) aria-current="page" @endif
                            class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200 {{ $isActive ? 'bg-accent/12 text-white shadow-[inset_0_0_0_1px_rgba(249,115,22,0.28)]' : 'text-slate-400 hover:bg-white/[0.045] hover:text-slate-100' }}"
                        >
                            <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-accent transition-all duration-200 {{ $isActive ? 'opacity-100 scale-y-100' : 'opacity-0 scale-y-50' }}"></span>
                            <span class="{{ $isActive ? 'text-accent' : 'text-slate-500 group-hover:text-slate-300' }} transition-colors duration-200">
                                <x-app.icon :name="$item['icon']" />
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    {{-- Catalog footer --}}
    <div class="shrink-0 border-t border-line p-3">
        <div class="rounded-xl bg-panel/70 p-3 ring-1 ring-line/80">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-600">Catalog</span>
                <span class="rounded-md bg-accent/12 px-1.5 py-0.5 text-[10px] font-semibold text-accent">25 items</span>
            </div>
            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-white/[0.06]">
                <div class="h-full w-full rounded-full bg-gradient-to-r from-accent to-accent-soft"></div>
            </div>
            <p class="mt-2 text-[11px] leading-relaxed text-slate-500">5 subtopics loaded from the source document.</p>
        </div>
    </div>
</aside>
