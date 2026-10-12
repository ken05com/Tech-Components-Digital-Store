@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'layers',
    'href' => null,
    'active' => false,
    'accent' => false,
])

@php $tag = $href ? 'a' : 'div'; @endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    class="group relative flex items-center gap-3.5 overflow-hidden rounded-2xl border p-4 transition-all duration-200
        {{ $active
            ? 'border-accent/35 bg-accent/[0.08] shadow-[0_8px_30px_-16px_rgba(201,0,22,0.55)]'
            : 'border-line bg-panel hover:border-line-strong hover:bg-panel-raised' }}
        {{ $href ? 'hover:-translate-y-0.5' : '' }}"
>
    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl transition-colors duration-200
        {{ $active || $accent ? 'bg-accent/15 text-accent ring-1 ring-accent/25' : 'bg-surface-strong text-muted ring-1 ring-line group-hover:text-ink' }}">
        <x-app.icon :name="$icon" />
    </span>

    <span class="min-w-0 flex-1">
        <span class="block truncate text-[11px] font-medium uppercase tracking-[0.12em] text-muted">{{ $label }}</span>
        <span class="mt-0.5 flex items-baseline gap-2">
            <span class="text-[22px] font-bold leading-none tracking-tight text-ink">{{ $value }}</span>
            @if ($hint)
                <span class="truncate text-[11px] text-muted">{{ $hint }}</span>
            @endif
        </span>
    </span>

    @if ($href)
        <span class="absolute right-3 top-3 text-muted transition-all duration-200 group-hover:right-2.5 group-hover:top-2.5 group-hover:text-accent">
            <x-app.icon name="arrow-right" />
        </span>
    @endif
</{{ $tag }}>
