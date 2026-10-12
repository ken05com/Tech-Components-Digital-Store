<x-layouts.storefront :page-title="$pageTitle">
    <div class="mx-auto w-full max-w-[1440px] px-4 pt-8 pb-16 sm:px-6 lg:px-10">

        {{-- Breadcrumbs --}}
        <nav class="mb-6 flex items-center gap-2 pl-2 text-[12.5px] font-semibold text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="ml-[45px] transition hover:text-brand">Home</a>
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5 opacity-40" />
            <a href="{{ route('products.index') }}" class="transition hover:text-brand">Shop</a>
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5 opacity-40" />
            <span class="text-ink">{{ $category->label() }}</span>
        </nav>

        {{-- Category Showcase Header --}}
        <div class="mb-8 rounded-3xl border border-line-soft bg-surface p-6 sm:p-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 rounded-full border border-brand/20 bg-brand/[0.06] px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.16em] text-brand">
                        Category Showcase
                    </span>
                    <h1 class="mt-4 text-[32px] font-extrabold tracking-tight text-ink sm:text-[42px]">{{ $category->label() }}</h1>
                    <p class="mt-2 text-[14.5px] leading-relaxed text-muted">{{ $category->description() }}</p>
                </div>

                {{-- Category Stats --}}
                <div class="flex flex-wrap gap-3 lg:justify-end">
                    <div class="rounded-2xl border border-line-soft bg-white p-4 min-w-[130px]">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-muted">Available</p>
                        <p class="mt-1 text-[20px] font-extrabold text-ink">{{ $summary['products'] }} Items</p>
                    </div>
                    @if ($summary['entry'] > 0)
                        <div class="rounded-2xl border border-line-soft bg-white p-4 min-w-[130px]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted">Starting From</p>
                            <p class="mt-1 text-[20px] font-extrabold text-brand">₱{{ number_format($summary['entry']) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Search & Sort Filter Bar --}}
        <div class="mb-8 rounded-2xl border border-line-soft bg-surface p-4">
            <form method="GET" action="{{ route('categories.show', $category->slug()) }}" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted">
                        <x-storefront.icon name="search" class="h-4.5 w-4.5" />
                    </span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search inside {{ $category->label() }}…"
                        class="h-11 w-full rounded-full border border-line-soft bg-white pl-11 pr-4 text-[13.5px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                    >
                </div>

                <div class="flex items-center gap-3">
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        aria-label="Sort products"
                        class="h-11 rounded-full border border-line-soft bg-white px-5 text-[13.5px] text-ink focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                    >
                        <option value="default" @selected(($filters['sort'] ?? '') === 'default')>Default Sorting</option>
                        <option value="price-asc" @selected(($filters['sort'] ?? '') === 'price-asc')>Price: Low to High</option>
                        <option value="price-desc" @selected(($filters['sort'] ?? '') === 'price-desc')>Price: High to Low</option>
                        <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name: A to Z</option>
                    </select>

                    <button
                        type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark"
                    >
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Product Grid Showcase --}}
        @if ($products->count() > 0)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($products as $product)
                    <x-storefront.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-line-soft bg-surface py-20 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand/10 text-brand">
                    <x-storefront.icon name="search" class="h-8 w-8" />
                </div>
                <h3 class="mt-4 text-[18px] font-extrabold text-ink">No products found in {{ $category->label() }}</h3>
                <p class="mt-1 text-[13.5px] text-muted">Try adjusting your search query or reset filters.</p>
                <a href="{{ route('categories.show', $category->slug()) }}" class="mt-6 inline-flex h-11 items-center rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                    Reset search
                </a>
            </div>
        @endif

    </div>
</x-layouts.storefront>
