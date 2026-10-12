@props([
    'term' => null,
    'resetUrl' => null,
])

<div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-surface-strong text-muted">
        <x-app.icon name="search" />
    </span>
    <div>
        <p class="text-[14px] font-semibold text-ink">No products found</p>
        <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-muted">
            @if ($term)
                Nothing in the catalog matches &ldquo;<span class="text-muted">{{ $term }}</span>&rdquo;. Try a different keyword or clear the filters.
            @else
                This category has no products yet. Add one to get started.
            @endif
        </p>
    </div>
    @if ($resetUrl)
        <a href="{{ $resetUrl }}" class="mt-1 inline-flex h-9 items-center gap-2 rounded-xl bg-surface-strong px-3.5 text-[12.5px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40">
            <x-app.icon name="refresh" />
            Reset filters
        </a>
    @endif
</div>
