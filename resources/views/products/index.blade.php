<x-layouts.storefront :page-title="$pageTitle ?? 'Shop'">
    <div class="mx-auto w-full max-w-[1440px] px-4 pt-8 pb-16 sm:px-6 lg:px-10">
        {{-- Shop Header --}}
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="mt-3 text-[32px] font-extrabold tracking-tight text-ink sm:text-[40px]">Shop All Gadgets</h1>
                <p class="mt-1 text-[14px] text-muted">
                    Explore {{ $totalProducts }} high-performance components, accessories, and tech essentials.
                </p>
            </div>

            {{-- Quick Category Filter Pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('products.index') }}"
                    class="rounded-full px-4 py-2 text-[13px] font-bold transition {{ ($filters['category'] ?? 'all') === 'all' ? 'bg-brand text-white shadow-sm' : 'border border-line-soft bg-surface text-ink hover:border-brand hover:text-brand' }}"
                >
                    All Products
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('products.index', ['category' => $category->slug()]) }}"
                        class="rounded-full px-4 py-2 text-[13px] font-bold transition {{ ($filters['category'] ?? '') === $category->slug() ? 'bg-brand text-white shadow-sm' : 'border border-line-soft bg-surface text-ink hover:border-brand hover:text-brand' }}"
                    >
                        {{ $category->shortLabel() }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search & Sort Filter Bar --}}
        <div class="mb-8 rounded-2xl border border-line-soft bg-surface p-4">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-muted">
                        <x-storefront.icon name="search" class="h-4.5 w-4.5" />
                    </span>
                    <input
                        type="search"
                        name="search"
                        value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search products by name, description or specs…"
                        class="h-11 w-full rounded-full border border-line-soft bg-white pl-11 pr-4 text-[13.5px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                    >
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="category" value="{{ $filters['category'] ?? 'all' }}">
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

        {{-- Product Grid --}}
        @if ($products->count() > 0)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($products as $product)
                    <x-storefront.product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-6 flex justify-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-line-soft bg-surface py-20 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand/10 text-brand">
                    <x-storefront.icon name="search" class="h-8 w-8" />
                </div>
                <h3 class="mt-4 text-[18px] font-extrabold text-ink">No products found</h3>
                <p class="mt-1 text-[13.5px] text-muted">Try adjusting your search or category filters.</p>
                <a href="{{ route('products.index') }}" class="mt-6 inline-flex h-11 items-center rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                    Reset filters
                </a>
            </div>
        @endif
    </div>
</x-layouts.storefront>
