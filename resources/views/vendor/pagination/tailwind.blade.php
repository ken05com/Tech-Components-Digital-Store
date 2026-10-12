@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <div class="flex items-center justify-between gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-white ring-1 ring-line-soft cursor-not-allowed rounded-none leading-5">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-bold text-ink bg-white ring-1 ring-line-soft leading-5 rounded-none transition hover:ring-ink/50 focus:outline-none focus:ring-2 focus:ring-ink/20">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-bold text-ink bg-white ring-1 ring-line-soft leading-5 rounded-none transition hover:ring-ink/50 focus:outline-none focus:ring-2 focus:ring-ink/20">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-muted bg-white ring-1 ring-line-soft cursor-not-allowed rounded-none leading-5">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>
    </nav>
@endif
