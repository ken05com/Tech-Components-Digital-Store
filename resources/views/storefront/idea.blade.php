<x-layouts.storefront>

    <section class="mx-auto w-full max-w-[1440px] px-4 pt-10 sm:px-6 lg:px-10">
        <nav class="text-[12px] font-semibold text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-brand">Home</a>
            <span class="mx-1.5">/</span>
            <a href="{{ route('ideas') }}" class="transition hover:text-brand">Ideas</a>
            <span class="mx-1.5">/</span>
            <span class="text-ink">{{ $idea['title'] }}</span>
        </nav>

        <div class="relative mt-6 flex h-72 items-end overflow-hidden rounded-[28px] sm:h-80">
            <span class="absolute inset-0 bg-gradient-to-br {{ $idea['bg'] }}"></span>
            <span class="absolute inset-0 grid place-items-center">
                <x-storefront.icon :name="$idea['icon']" class="h-28 w-28 text-white/80" />
            </span>
            <span class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></span>

            <div class="relative z-10 p-6 sm:p-10">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.16em] text-white">
                    {{ number_format($idea['count']) }} ideas
                </span>
                <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">{{ $idea['title'] }}</h1>
            </div>
        </div>

        <div class="mt-8 max-w-2xl">
            <p class="text-[14px] leading-relaxed text-muted">{{ $idea['blurb'] }}</p>
            <p class="mt-3 rounded-2xl border border-dashed border-line-soft bg-surface px-4 py-4 text-[12.5px] text-muted">
                This editorial page is a placeholder — the full write-up and product picks will be added in a future update.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center gap-2 rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                    Shop the catalog
                    <x-storefront.icon name="arrow-right" class="h-4 w-4" />
                </a>
                <a href="{{ route('ideas') }}" class="inline-flex h-11 items-center gap-2 rounded-full border border-ink/10 bg-white px-6 text-[13.5px] font-bold text-ink transition hover:border-brand hover:text-brand">
                    <x-storefront.icon name="chevron-left" class="h-4 w-4" />
                    All ideas
                </a>
            </div>
        </div>
    </section>

</x-layouts.storefront>