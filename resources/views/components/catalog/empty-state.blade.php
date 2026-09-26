@props([
    'term' => null,
    'resetUrl' => null,
])

<div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
    <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-shell/60 text-slate-500">
        <x-app.icon name="search" />
    </span>
    <div>
        <p class="text-[14px] font-semibold text-slate-200">No products found</p>
        <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-slate-500">
            @if ($term)
                Nothing in the catalog matches &ldquo;<span class="text-slate-400">{{ $term }}</span>&rdquo;. Try a different keyword or clear the filters.
            @else
                This category has no products yet. Add one to get started.
            @endif
        </p>
    </div>
    @if ($resetUrl)
        <a href="{{ $resetUrl }}" class="mt-1 inline-flex h-9 items-center gap-2 rounded-xl bg-white/[0.06] px-3.5 text-[12.5px] font-semibold text-slate-200 ring-1 ring-line transition hover:bg-white/10">
            <x-app.icon name="refresh" />
            Reset filters
        </a>
    @endif
</div>
