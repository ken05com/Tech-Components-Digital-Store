@props([
    'title',
    'description' => null,
    'eyebrow' => 'Catalog',
    'badge' => null,
    'badgeTone' => 'accent',
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <nav class="flex items-center gap-1.5 text-[11px] font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
            <span class="text-slate-700">/</span>
            <span class="text-slate-400">{{ $eyebrow }}</span>
            @if ($badge)
                <span class="text-slate-700">/</span>
                <span class="text-slate-400">{{ $badge }}</span>
            @endif
        </nav>

        <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
            <h1 class="truncate text-[22px] font-bold tracking-tight text-white sm:text-2xl">{{ $title }}</h1>
            @if ($badge)
                <x-ui.badge :label="$eyebrow" :tone="$badgeTone" />
            @endif
        </div>

        @if ($description)
            <p class="mt-1 max-w-2xl text-[13px] leading-relaxed text-slate-400">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
