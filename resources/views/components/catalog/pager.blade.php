@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex flex-col items-center justify-between gap-3 border-t border-line px-4 py-3 sm:flex-row" aria-label="Pagination">
        <p class="text-[11px] text-slate-500">
            Page <span class="font-semibold text-slate-300">{{ $paginator->currentPage() }}</span>
            of <span class="font-semibold text-slate-300">{{ $paginator->lastPage() }}</span>
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-shell/40 text-slate-700" aria-disabled="true">
                    <x-app.icon name="chevron-left" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-shell/60 text-slate-400 transition hover:border-accent/40 hover:text-accent">
                    <x-app.icon name="chevron-left" />
                </a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="grid h-8 min-w-8 place-items-center rounded-lg bg-accent px-2.5 text-[12px] font-bold tabular-nums text-white shadow-[0_6px_16px_-8px_rgba(249,115,22,0.9)]">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="grid h-8 min-w-8 place-items-center rounded-lg border border-line bg-shell/60 px-2.5 text-[12px] font-semibold tabular-nums text-slate-400 transition hover:border-accent/40 hover:text-accent">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-shell/60 text-slate-400 transition hover:border-accent/40 hover:text-accent">
                    <x-app.icon name="chevron-right" />
                </a>
            @else
                <span class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-shell/40 text-slate-700" aria-disabled="true">
                    <x-app.icon name="chevron-right" />
                </span>
            @endif
        </div>
    </nav>
@endif
