@php
    $color = \App\Catalog\ChartPalette::color($product->category->slug());
@endphp

<x-layouts.app :active="'category-'.$product->category->slug()" :page-title="$pageTitle">
    {{-- Breadcrumb --}}
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-muted">/</span>
        <a href="{{ route('products.index') }}" class="transition hover:text-accent">Catalog</a>
        <span class="text-muted">/</span>
        <a href="{{ route('categories.overview', $product->category->slug()) }}" class="transition hover:text-accent">{{ $product->category->label() }}</a>
        <span class="text-muted">/</span>
        <span class="text-muted">{{ $product->name }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-5">

        {{-- Picture + key facts --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <div class="relative overflow-hidden rounded-xl border border-line bg-surface-strong">
                    <div class="grid h-64 place-items-center p-6 sm:h-72">
                        <img
                            src="{{ $product->imageUrl() }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-contain"
                            onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
                        >
                    </div>
                    <span class="absolute left-3 top-3 rounded-lg bg-accent px-2.5 py-1 text-[11px] font-bold tabular-nums text-white shadow-lg">
                        ID {{ $product->displayId() }}
                    </span>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <x-ui.badge :label="$product->category->label()" tone="accent" dot />
                    <x-ui.badge label="In catalog" tone="emerald" dot />
                </div>

                <h1 class="mt-3 text-[22px] font-bold leading-tight tracking-tight text-ink">{{ $product->name }}</h1>
                <p class="mt-2 text-[13px] leading-relaxed text-muted">{{ $product->description }}</p>

                <div class="mt-5 rounded-xl border border-accent/20 bg-accent/[0.06] p-4">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-accent/80">Price</p>
                    <p class="mt-1.5 text-[26px] font-bold leading-none tabular-nums text-ink">{{ $product->priceLabel() }}</p>
                    @if ($product->hasPriceRange())
                        <p class="mt-1.5 text-[11.5px] text-muted">
                            From ₱{{ $product->formattedMin() }} up to ₱{{ $product->formattedMax() }}
                        </p>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('products.edit', $product->id) }}" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(201,0,22,0.8)] transition hover:bg-accent-soft">
                        <x-app.icon name="edit" />
                        Edit
                    </a>
                    <a href="{{ route('categories.overview', $product->category->slug()) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40">
                        <x-app.icon name="layers" />
                        {{ $product->category->shortLabel() }}
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[13px] font-semibold text-ink">Record</h2>
                <dl class="mt-3 space-y-2.5 text-[12.5px]">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Catalog ID</dt>
                        <dd class="font-semibold tabular-nums text-ink">#{{ $product->id }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Row ID</dt>
                        <dd class="font-semibold tabular-nums text-ink">{{ $product->displayId() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Starting price</dt>
                        <dd class="font-semibold tabular-nums text-ink">₱{{ $product->formattedMin() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Maximum price</dt>
                        <dd class="font-semibold tabular-nums text-ink">₱{{ $product->formattedMax() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Added</dt>
                        <dd class="font-semibold text-ink">{{ $product->createdAt ? \Illuminate\Support\Carbon::parse($product->createdAt)->format('M j, Y') : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Updated</dt>
                        <dd class="font-semibold text-ink">{{ $product->updatedAt ? \Illuminate\Support\Carbon::parse($product->updatedAt)->format('M j, Y') : '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Description panel + related --}}
        <div class="space-y-4 lg:col-span-3">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[14px] font-semibold text-ink">Product Details</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-line bg-surface-strong p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Description</p>
                        <p class="mt-2 text-[13px] leading-relaxed text-ink">{{ $product->description }}</p>
                    </div>
                    <div class="rounded-xl border border-line bg-surface-strong p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Category</p>
                        <p class="mt-2 text-[13px] leading-relaxed text-ink">
                            {{ $product->category->label() }}<br>
                            <span class="text-[11.5px] text-muted">{{ $product->category->description() }}</span>
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-surface-strong p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Price range</p>
                        <p class="mt-2 text-[13px] leading-relaxed text-ink">
                            {{ $product->priceLabel() }}<br>
                            <span class="text-[11.5px] text-muted">Midpoint ₱{{ $product->formattedAveragePrice() }}</span>
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-surface-strong p-4">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Availability</p>
                        <p class="mt-2 text-[13px] leading-relaxed text-ink">
                            Listed in catalog<br>
                            <span class="text-[11.5px] text-muted">{{ $related->count() + 1 }} items in {{ $product->category->shortLabel() }}</span>
                        </p>
                    </div>
                </div>
            </div>

            @if ($related->isNotEmpty())
                <div class="rounded-2xl border border-line bg-panel p-5">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-[14px] font-semibold text-ink">More in {{ $product->category->label() }}</h2>
                        <a href="{{ route('categories.overview', $product->category->slug()) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-muted transition hover:text-accent">
                            View subtopic
                            <x-app.icon name="arrow-right" />
                        </a>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($related as $item)
                            <x-catalog.product-card :product="$item" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
