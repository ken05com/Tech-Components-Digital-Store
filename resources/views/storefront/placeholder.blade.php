<x-layouts.storefront>

    <section class="mx-auto w-full max-w-[1440px] px-4 pt-10 sm:px-6 lg:px-10">
        <nav class="text-[12px] font-semibold text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-brand">Home</a>
            <span class="mx-1.5">/</span>
            <span class="text-ink">{{ $title }}</span>
        </nav>

        <div class="mt-6 rounded-[28px] border border-line-soft bg-[#f2f2f4] px-6 py-14 text-center sm:px-10 sm:py-20">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-brand/20 bg-white px-3 py-1.5 text-[10.5px] font-bold uppercase tracking-[0.2em] text-brand">
                <x-storefront.icon name="sparkles" class="h-3.5 w-3.5" />
                {{ $eyebrow }}
            </span>

            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">{{ $title }}</h1>
            <p class="mx-auto mt-3 max-w-md text-[13.5px] leading-relaxed text-muted">{{ $message }}</p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center gap-2 rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                    Browse the catalog
                    <x-storefront.icon name="arrow-right" class="h-4 w-4" />
                </a>
                <a href="{{ route('ideas') }}" class="inline-flex h-11 items-center rounded-full border border-ink/10 bg-white px-6 text-[13.5px] font-bold text-ink transition hover:border-brand hover:text-brand">
                    Explore ideas
                </a>
            </div>
        </div>
    </section>

</x-layouts.storefront>