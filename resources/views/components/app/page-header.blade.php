@props([
    'title',
    'description' => null,
    'eyebrow' => 'Catalog',
    'badge' => null,
    'badgeTone' => 'accent',
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
            <h1 class="truncate text-[22px] font-bold tracking-tight text-ink sm:text-2xl">{{ $title }}</h1>
            @if ($badge)
                <x-ui.badge :label="$eyebrow" :tone="$badgeTone" />
            @endif
        </div>

        @if ($description)
            <p class="mt-1 max-w-2xl text-[13px] leading-relaxed text-muted">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
