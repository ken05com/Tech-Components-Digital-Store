<section class="mx-auto w-full max-w-[1440px] px-4 pt-12 sm:px-6 lg:px-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-[22px] font-extrabold tracking-tight text-ink sm:text-[26px]">Trending Products</h2>
            <p class="mt-1 text-[12.5px] text-muted">Fresh picks from the live catalog — {{ $totalProducts }} products in stock.</p>
        </div>
        <a href="{{ route('products.index') }}" class="group inline-flex shrink-0 items-center gap-1.5 text-[12.5px] font-bold text-brand transition hover:gap-2.5">
            View All
            <x-storefront.icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
        </a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 lg:grid-cols-6">
        @forelse ($trending as $product)
            <x-storefront.product-card :product="$product" />
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-line-soft bg-surface p-10 text-center">
                <p class="text-[14px] font-semibold text-ink">No products available right now.</p>
                <p class="mt-1 text-[12.5px] text-muted">Check back soon — the catalog is loaded fresh from the database.</p>
            </div>
        @endforelse
    </div>
</section>