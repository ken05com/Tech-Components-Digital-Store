@php
    $hasRange = $product->hasPriceRange();
    $shareUrl = url()->current();
    $variantOptions = collect($product->variants)
        ->map(fn ($variant, $index) => ['name' => $variant['name'], 'price' => $product->variantPriceAt($index)])
        ->values()
        ->all();
@endphp

<x-layouts.storefront :page-title="$pageTitle">
    <div class="mx-auto w-full max-w-[1440px] px-4 pt-6 pb-16 sm:px-6 lg:px-10">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-[12.5px] font-semibold text-muted" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-brand">Home</a>
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5 opacity-40" />
            <a href="{{ route('products.index') }}" class="transition hover:text-brand">Shop</a>
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5 opacity-40" />
            <a href="{{ route('categories.show', $product->category->slug()) }}" class="transition hover:text-brand">{{ $product->category->label() }}</a>
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5 opacity-40" />
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div
            class="grid gap-8 lg:grid-cols-5"
            x-data="{
                variants: @js($variantOptions),
                selected: 0,
                get chosen() { return this.variants[this.selected] ?? null },
                get activePrice() { return this.chosen ? this.chosen.price : {{ $product->priceMin }} },
                get activePriceLabel() { return '₱' + this.activePrice.toLocaleString('en-US') },
                get listPrice() { return {{ $product->priceMax }} },
                get hasRange() { return {{ $hasRange ? 'true' : 'false' }} },
                get discount() { return (this.hasRange && this.listPrice > 0) ? Math.round((this.listPrice - this.activePrice) / this.listPrice * 100) : 0 },
                get showDiscount() { return this.discount > 0 },
            }"
        >

            {{-- LEFT PANEL — Media & promotions (approx 40%) ------------------ --}}
            <div class="lg:col-span-2">
                {{-- Brand --}}
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand text-[14px] font-extrabold text-white">T</span>
                    <span class="text-[13px] font-extrabold tracking-tight text-ink">Tech Components</span>
                </div>

                {{-- Hero image --}}
                <div class="relative mt-4 overflow-hidden rounded-3xl border border-line-soft bg-surface">
                    <div class="grid aspect-square place-items-center p-8">
                        <img
                            src="{{ $product->imageUrl() }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-contain"
                            onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
                        >
                    </div>

                    {{-- Spec badges (floating to the left) --}}
                    <div class="absolute left-3 top-1/2 hidden -translate-y-1/2 flex-col gap-2 sm:flex">
                        <span class="rounded-full border border-line-soft bg-white/95 px-3 py-1 text-[11px] font-bold text-ink shadow-sm">{{ $product->category->shortLabel() }}</span>
                        <span class="rounded-full border border-line-soft bg-white/95 px-3 py-1 text-[11px] font-bold shadow-sm {{ $product->stock > 0 ? 'text-emerald-600' : 'text-muted' }}">
                            {{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}
                        </span>
                    </div>

                    <span x-show="showDiscount" x-cloak class="absolute right-3 top-3 rounded-full bg-brand px-3 py-1 text-[11px] font-extrabold text-white shadow-sm" x-text="'-' + discount + '%'">-0%</span>
                </div>

                {{-- Thumbnail carousel --}}
                <div class="mt-4 flex items-center gap-2">
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full border border-line-soft text-muted transition hover:text-brand disabled:opacity-40" disabled aria-label="Previous image">
                        <x-storefront.icon name="chevron-left" class="h-4 w-4" />
                    </button>
                    <div class="h-16 w-16 overflow-hidden rounded-xl border-2 border-brand bg-surface p-1.5">
                        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }} thumbnail" class="h-full w-full object-contain" onerror="this.src='{{ asset('images/products/placeholder.svg') }}'">
                    </div>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-full border border-line-soft text-muted transition hover:text-brand disabled:opacity-40" disabled aria-label="Next image">
                        <x-storefront.icon name="chevron-right" class="h-4 w-4" />
                    </button>
                </div>

                {{-- Share + Favorite --}}
                <div class="mt-5 flex items-center gap-2 border-t border-line-soft pt-4">
                    <span class="text-[12px] font-semibold text-muted">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="grid h-8 w-8 place-items-center rounded-full border border-line-soft text-[11px] font-bold text-ink transition hover:border-brand hover:text-brand" aria-label="Share on Facebook">f</a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="grid h-8 w-8 place-items-center rounded-full border border-line-soft text-[11px] font-bold text-ink transition hover:border-brand hover:text-brand" aria-label="Share on X">X</a>
                    <span class="mx-1 h-5 w-px bg-line-soft"></span>
                    <button
                        type="button"
                        x-on:click="$store.storefront.toggle({ id: {{ $product->id }}, name: @js($product->name), url: @js(route('products.detail', $product->id)), image: @js($product->imageUrl()) })"
                        :aria-pressed="$store.storefront.has({{ $product->id }})"
                        class="inline-flex h-9 items-center gap-2 rounded-full border border-line-soft px-4 text-[12px] font-bold text-ink transition hover:border-brand hover:text-brand"
                    >
                        <template x-if="$store.storefront.has({{ $product->id }})">
                            <x-storefront.icon name="heart" filled class="h-4 w-4 text-brand" />
                        </template>
                        <template x-if="! $store.storefront.has({{ $product->id }})">
                            <x-storefront.icon name="heart" class="h-4 w-4" />
                        </template>
                        Favorite
                    </button>
                </div>
            </div>

            {{-- RIGHT PANEL — Information & checkout (approx 60%) ------------- --}}
            <div class="lg:col-span-3">
                {{-- Title box --}}
                <span class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.12em] text-brand">Preferred</span>
                <h1 class="mt-2 text-[24px] font-extrabold leading-tight tracking-tight text-ink sm:text-[28px]">{{ $product->name }}</h1>
                <p class="mt-1.5 text-[13px] leading-relaxed text-muted">{{ $product->description }}</p>

                {{-- Metrics row --}}
                <div class="mt-3 flex flex-wrap items-center gap-3 text-[12.5px] font-semibold text-muted">
                    <span class="inline-flex items-center gap-1.5">
                        <x-storefront.icon name="tag" class="h-4 w-4 text-brand" />
                        {{ $product->category->label() }}
                    </span>
                    <span class="h-4 w-px bg-line-soft"></span>
                    <span class="{{ $product->stock > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                        {{ $product->stock > 0 ? $product->stock.' in stock' : 'Out of stock' }}
                    </span>
                    <a href="{{ route('categories.show', $product->category->slug()) }}" class="ml-auto transition hover:text-brand">View category</a>
                </div>

                {{-- Variant dropdown --}}
                @if ($product->hasVariants())
                    <div class="mt-5">
                        <label for="variant" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.12em] text-muted">Variants</label>
                        <div class="relative">
                            <select
                                id="variant"
                                x-model.number="selected"
                                class="h-12 w-full appearance-none rounded-none border-2 border-ink/40 bg-white px-4 pr-11 text-[14px] font-bold text-ink outline-none transition hover:border-ink/60 focus:border-ink focus:ring-2 focus:ring-ink/20"
                            >
                                @foreach ($variantOptions as $index => $variant)
                                    <option value="{{ $index }}">{{ $variant['name'] }}{{ $variant['price'] > 0 ? ' — ₱'.number_format($variant['price']) : '' }}</option>
                                @endforeach
                            </select>
                            <x-storefront.icon name="chevron-down" class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-ink" />
                        </div>
                    </div>
                @endif

                {{-- Price block --}}
                <div class="mt-5 rounded-2xl border border-line-soft bg-surface p-5">
                    <span x-show="showDiscount" x-cloak class="inline-block rounded-md bg-brand px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-white">Today only!</span>
                    <div class="mt-2 flex flex-wrap items-end gap-3">
                        <span class="text-[32px] font-extrabold leading-none tracking-tight text-brand" x-text="activePriceLabel">₱{{ $product->formattedMin() }}</span>
                        <span x-show="hasRange" x-cloak class="text-[15px] font-semibold text-muted line-through">₱{{ $product->formattedMax() }}</span>
                        <span x-show="showDiscount" x-cloak class="rounded-md bg-brand/10 px-2 py-0.5 text-[12px] font-extrabold text-brand" x-text="'-' + discount + '%'">-0%</span>
                    </div>
                    @if ($hasRange)
                        <p class="mt-2 text-[11.5px] text-muted">Price ranges from ₱{{ $product->formattedMin() }} up to ₱{{ $product->formattedMax() }}.</p>
                    @endif
                </div>

                {{-- Shipping & guarantees --}}
                <div class="mt-5 space-y-2.5">
                    <div class="flex items-start gap-3 text-[12.5px] text-ink">
                        <x-storefront.icon name="truck" class="mt-0.5 h-5 w-5 shrink-0 text-brand" />
                        <span>Free delivery on orders over ₱2,000 — usually arrives within 2–5 days.</span>
                    </div>
                    <div class="flex items-start gap-3 text-[12.5px] text-ink">
                        <x-storefront.icon name="shield-check" class="mt-0.5 h-5 w-5 shrink-0 text-brand" />
                        <span>Free &amp; easy returns within 7 days.</span>
                    </div>
                </div>

                {{-- Quantity & CTA --}}
                @if ($product->stock > 0)
                    <div class="mt-6 border-t border-line-soft pt-5" x-data="{ qty: 1, max: {{ $product->stock }} }">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="text-[12.5px] font-bold text-ink">Quantity</span>
                            <div class="flex items-center overflow-hidden rounded-full border border-line-soft">
                                <button type="button" x-on:click="qty = Math.max(1, qty - 1)" class="grid h-9 w-9 place-items-center text-lg text-ink transition hover:bg-surface" aria-label="Decrease quantity">&minus;</button>
                                <input type="text" x-model.number="qty" inputmode="numeric" class="h-9 w-12 border-x border-line-soft bg-white text-center text-[13px] font-bold text-ink focus:outline-none" aria-label="Quantity">
                                <button type="button" x-on:click="qty = Math.min(max, qty + 1)" class="grid h-9 w-9 place-items-center text-lg text-ink transition hover:bg-surface" aria-label="Increase quantity">+</button>
                            </div>
                            <span class="text-[11.5px] font-bold uppercase tracking-[0.1em] text-emerald-600">In stock</span>
                        </div>

                        @php
                            $canManage = auth()->user()?->can('manage-products');
                        @endphp
                        @if (!$canManage)
                            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                                <form method="POST" action="{{ route('cart.store') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" :value="qty">
                                    <input type="hidden" name="variant" :value="chosen ? chosen.name : ''">
                                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full border-2 border-brand bg-white text-[14px] font-bold text-brand transition hover:bg-brand/5">
                                        <x-storefront.icon name="cart" class="h-5 w-5" />
                                        Add To Cart
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('cart.store') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" :value="qty">
                                    <input type="hidden" name="variant" :value="chosen ? chosen.name : ''">
                                    <input type="hidden" name="redirect" value="checkout">
                                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center rounded-full bg-brand text-[14px] font-bold text-white shadow-sm transition hover:bg-brand-dark">
                                        Buy Now
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-5 rounded-2xl border border-line-soft bg-surface p-4 text-center text-[12.5px] text-muted">
                                Signed in as Catalog Administrator. Cart purchases are reserved for storefront customers.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-6 rounded-2xl border border-dashed border-line-soft bg-surface p-5 text-center">
                        <p class="text-[14px] font-bold text-ink">Currently out of stock</p>
                        <p class="mt-1 text-[12.5px] text-muted">Check back soon or browse more in {{ $product->category->label() }}.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="mt-14">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="text-[20px] font-extrabold tracking-tight text-ink sm:text-[24px]">More in {{ $product->category->label() }}</h2>
                    <a href="{{ route('categories.show', $product->category->slug()) }}" class="group inline-flex shrink-0 items-center gap-1.5 text-[12.5px] font-bold text-brand transition hover:gap-2.5">
                        View all
                        <x-storefront.icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                    </a>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($related as $item)
                        <x-storefront.product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.storefront>
