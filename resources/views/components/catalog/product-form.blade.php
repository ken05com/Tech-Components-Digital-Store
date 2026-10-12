@props([
    'product' => null,
    'categories',
    'selectedCategory',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Add Product',
    'successDialog' => null,
    'canDelete' => false,
    'cancelUrl' => null,
])

@php
    $isEdit = $product !== null;
    $variantsText = collect($product?->variants ?? [])
        ->map(fn ($variant) => $variant['name'].(($variant['price'] ?? null) ? ' | '.$variant['price'] : ''))
        ->implode("\n");

    // Send Cancel back to the page the form was opened from (the product
    // overview table, search results, etc.), but only when that URL is a
    // same-site page other than the form itself. Otherwise fall back.
    $previous = url()->previous();
    $previousIsReusable = $previous !== ''
        && $previous !== url()->current()
        && str_starts_with($previous, url('/').'/');
    $cancelHref = $cancelUrl
        ?? ($previousIsReusable ? $previous : ($isEdit ? route('products.show', $product->id) : route('dashboard')));
@endphp

<div x-data="productForm(@js($product?->imageUrl()))">
<div class="grid gap-5 lg:grid-cols-3">
<form
    id="product-form"
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    class="space-y-5 lg:col-span-2"
>
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    {{-- Left: the fields --}}
        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="tag" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Product Details</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Name</label>
                    <input
                        id="name"
                        x-ref="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $product?->name) }}"
                        required
                        maxlength="100"
                        placeholder="e.g. CPU / Processor"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('name'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Description</label>
                    <textarea
                        id="description"
                        x-ref="description"
                        name="description"
                        rows="3"
                        required
                        maxlength="255"
                        placeholder="Short, clear summary of the product."
                        @class([
                            'w-full resize-y rounded-xl border bg-surface-strong px-3.5 py-3 text-[13px] leading-relaxed text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('description'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('description'),
                        ])
                    >{{ old('description', $product?->description) }}</textarea>
                    <div class="mt-1.5 flex items-center justify-between">
                        @error('description') <p class="text-[11.5px] text-rose-600">{{ $message }}</p> @else <span></span> @enderror
                        <span class="text-[11px] text-muted" x-text="`${descriptionLength()}/255`"></span>
                    </div>
                </div>

                <div>
                    <label for="category" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Category</label>
                    <select
                        id="category"
                        x-ref="category"
                        name="category"
                        required
                        @class([
                            'h-11 w-full appearance-none rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('category'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('category'),
                        ])
                    >
                        @foreach ($categories as $option)
                            <option value="{{ $option->value }}" @selected(old('category', $selectedCategory->value) === $option->value)>{{ $option->label() }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">
                        ID <span class="normal-case tracking-normal text-muted">(row number inside the category)</span>
                    </label>
                    <input
                        id="category_id"
                        name="category_id"
                        type="number"
                        min="1"
                        max="99"
                        value="{{ old('category_id', $product?->categoryId) }}"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] tabular-nums text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('category_id'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('category_id'),
                        ])
                    >
                    @error('category_id') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Pricing</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="price_min" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Starting Price (₱)</label>
                    <input
                        id="price_min"
                        x-ref="price_min"
                        name="price_min"
                        type="number"
                        min="0"
                        step="1"
                        required
                        value="{{ old('price_min', $product?->priceMin) }}"
                        x-on:input="syncPrice()"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] tabular-nums text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('price_min'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('price_min'),
                        ])
                    >
                    @error('price_min') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price_max" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Maximum Price (₱)</label>
                    <input
                        id="price_max"
                        x-ref="price_max"
                        name="price_max"
                        type="number"
                        min="0"
                        step="1"
                        required
                        value="{{ old('price_max', $product?->priceMax ?? $product?->priceMin) }}"
                        x-on:input="syncPrice()"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] tabular-nums text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('price_max'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('price_max'),
                        ])
                    >
                    @error('price_max') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="stock" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Stock on hand</label>
                @if ($isEdit)
                    <input type="hidden" x-ref="stock" value="{{ $product->stock }}">
                    <div class="flex items-center gap-2 rounded-xl border border-line bg-surface-strong px-3.5 py-2.5">
                        <span class="text-[13px] font-semibold tabular-nums text-ink">{{ $product->stock }}</span>
                        <span class="text-[11px] text-muted">unit(s) in stock &middot; adjust from the product page to keep a ledger.</span>
                    </div>
                @else
                    <input
                        id="stock"
                        name="stock"
                        x-ref="stock"
                        type="number"
                        min="0"
                        step="1"
                        value="{{ old('stock', 0) }}"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] tabular-nums text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('stock'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('stock'),
                        ])
                    >
                    @error('stock') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                @endif
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 rounded-xl border border-line bg-surface-strong px-3.5 py-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Preview</span>
                <span class="rounded-lg bg-accent/[0.08] px-2.5 py-1 text-[12.5px] font-semibold tabular-nums text-accent ring-1 ring-inset ring-accent/20" x-text="priceLabel()">₱0</span>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Variants</h2>
            </div>

            <label for="variants" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">
                Options <span class="normal-case tracking-normal text-muted">(one per line — "Name" or "Name | Price")</span>
            </label>
            <textarea
                id="variants"
                name="variants"
                x-ref="variants"
                rows="4"
                maxlength="2000"
                placeholder="e.g.&#10;16GB DDR4&#10;32GB DDR4 | 6000"
                @class([
                    'w-full resize-y rounded-xl border bg-surface-strong px-3.5 py-3 text-[13px] leading-relaxed text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                    'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('variants'),
                    'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('variants'),
                ])
            >{{ old('variants', $variantsText) }}</textarea>
            @error('variants') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
            <p class="mt-1.5 text-[11px] leading-relaxed text-muted">
                These appear as a dropdown on the storefront product page. Leave empty for no variants.
            </p>
        </div>
</form>

    {{-- Right: image + actions --}}
    <div class="space-y-5">
        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="eye" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Picture</h2>
            </div>

            <label
                for="image"
                class="group relative flex cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-xl border border-dashed border-line bg-surface-strong px-4 py-7 text-center transition hover:border-accent/45 hover:bg-accent/[0.04]"
            >
                <template x-if="preview">
                    <img :src="preview" alt="Selected product image preview" class="max-h-40 w-full object-contain">
                </template>
                <template x-if="! preview">
                    <span class="flex flex-col items-center gap-2">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-surface-strong text-muted ring-1 ring-line transition group-hover:text-accent">
                            <x-app.icon name="layers" />
                        </span>
                        <span class="text-[12.5px] font-semibold text-ink">Choose an image</span>
                        <span class="text-[11px] text-muted">JPG, PNG, WEBP or GIF &middot; max 2&nbsp;MB</span>
                    </span>
                </template>
                <input
                    id="image"
                    name="image"
                    type="file"
                    form="product-form"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="sr-only"
                    x-on:change="pickImage($event)"
                >
            </label>

            @error('image') <p class="mt-2 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror

            <p class="mt-3 text-[11px] leading-relaxed text-muted">
                Images are stored in <span class="text-muted">public/images/products</span>. Leave this empty on edit to keep the current picture.
            </p>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="flex flex-col gap-2">
                <button type="submit" form="product-form" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(201,0,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                    <x-app.icon name="plus" />
                    {{ $submitLabel }}
                </button>
                <button type="button" x-on:click="openPreview()" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40 hover:text-ink">
                    <x-app.icon name="eye" />
                    Preview
                </button>
                <a href="{{ $cancelHref }}" class="inline-flex h-11 items-center justify-center rounded-xl px-4 text-[13px] font-semibold text-muted transition hover:bg-line/40 hover:text-ink">
                    Cancel
                </a>
            </div>
        </div>

        @if ($isEdit && $canDelete)
            <div class="rounded-2xl border border-rose-500/20 bg-rose-500/[0.05] p-5">
                <h2 class="text-[13px] font-semibold text-rose-700">Danger zone</h2>
                <p class="mt-1.5 text-[11.5px] leading-relaxed text-rose-700/70">Moving this product to Deleted Items keeps its picture so it can be restored later.</p>
                <div class="mt-3.5">
                    <x-app.confirm-form
                        :action="route('products.destroy', $product->id)"
                        method="DELETE"
                        icon="trash"
                        title="Delete this product?"
                        :message="'<span class=&quot;font-semibold text-ink&quot;>'.e($product->name).'</span> will be moved to Deleted Items. A Super Admin can restore it.'"
                        confirm-label="Yes, delete"
                    />
                </div>
            </div>
        @endif
    </div>
</div>

    {{-- Preview modal --}}
    <div
        x-show="showPreviewFlag"
        x-cloak
        class="fixed inset-0 z-[65] grid place-items-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="product-preview-title"
    >
        <div
            x-show="showPreviewFlag"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            @click="showPreviewFlag = false"
            class="absolute inset-0 bg-black/75 backdrop-blur-sm"
        ></div>

        <div
            x-show="showPreviewFlag"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            @click.stop
            @keydown.escape.window="showPreviewFlag = false"
            class="relative flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-line bg-panel shadow-2xl"
        >
            {{-- Modal header --}}
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-line px-5 py-3.5">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                        <x-app.icon name="eye" />
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-ink">Storefront preview</p>
                        <p class="text-[11px] text-muted">How shoppers will see this product in the catalog.</p>
                    </div>
                </div>
                <button type="button" @click="showPreviewFlag = false" aria-label="Close preview" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition hover:bg-line/40 hover:text-ink">
                    <x-app.icon name="close" />
                </button>
            </div>

            {{-- Modal body --}}
            <div class="grid gap-6 overflow-y-auto p-5 sm:grid-cols-[240px_1fr] sm:p-6">
                {{-- Live storefront card --}}
                <div class="mx-auto w-full max-w-[240px]">
                    <article class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-line-soft bg-white">
                        <div class="relative block aspect-square overflow-hidden bg-surface">
                            <template x-if="preview">
                                <img :src="preview" alt="Product preview" class="h-full w-full object-contain p-4">
                            </template>
                            <template x-if="! preview">
                                <span class="grid h-full w-full place-items-center text-[12px] text-muted">No image selected</span>
                            </template>
                            <span class="absolute left-2.5 top-2.5 rounded-full border border-line-soft bg-white/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.08em] text-muted" x-text="previewCategory || 'Category'"></span>
                        </div>

                        <div class="flex flex-1 flex-col gap-1.5 p-3.5">
                            <span class="line-clamp-2 min-h-[2.35rem] text-[12.5px] font-semibold leading-snug text-ink" x-text="previewName || 'Untitled product'"></span>
                            <p class="mt-auto text-[14px] font-extrabold tracking-tight text-brand" x-text="previewPrice || '₱0'"></p>

                            <template x-if="previewStock > 0">
                                <span class="mt-1 inline-flex h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-brand/10 text-[11.5px] font-bold uppercase tracking-[0.08em] text-brand">
                                    <x-storefront.icon name="cart" class="h-4 w-4" />
                                    Add to cart
                                </span>
                            </template>
                            <template x-if="previewStock <= 0">
                                <span class="mt-1 inline-flex h-9 w-full items-center justify-center rounded-lg bg-surface text-[11.5px] font-bold uppercase tracking-[0.08em] text-muted">
                                    Out of stock
                                </span>
                            </template>

                            <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-[0.1em] text-muted">
                                View details
                                <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5" />
                            </span>
                        </div>
                    </article>
                </div>

                {{-- Details --}}
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-accent">Product details</p>
                    <h3 id="product-preview-title" class="mt-1 text-[20px] font-bold tracking-tight text-ink" x-text="previewName || 'Untitled product'"></h3>

                    <div class="mt-2.5 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-lg bg-accent/12 px-2 py-1 text-[11px] font-semibold text-accent ring-1 ring-inset ring-accent/25" x-text="previewCategory || 'Uncategorised'"></span>
                        <template x-if="previewStock > 0">
                            <span class="inline-flex items-center rounded-lg bg-emerald-500/12 px-2 py-1 text-[11px] font-semibold text-emerald-600 ring-1 ring-inset ring-emerald-500/25" x-text="previewStock + ' in stock'"></span>
                        </template>
                        <template x-if="previewStock <= 0">
                            <span class="inline-flex items-center rounded-lg bg-rose-500/12 px-2 py-1 text-[11px] font-semibold text-rose-600 ring-1 ring-inset ring-rose-500/25">Out of stock</span>
                        </template>
                    </div>

                    <p class="mt-3 text-[13px] leading-relaxed text-muted" x-text="previewDescription || 'No description yet.'"></p>

                    <template x-if="previewVariants.length">
                        <div class="mt-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Variants</p>
                            <select disabled aria-label="Product variants" class="mt-1.5 h-11 w-full rounded-xl border border-line bg-surface-strong px-3 text-[13px] text-ink">
                                <template x-for="(variant, index) in previewVariants" :key="index">
                                    <option x-text="variant.name + (variant.price ? ' — ' + variant.price : '')"></option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <p class="mt-4 inline-flex items-center rounded-lg bg-accent/[0.08] px-3 py-1.5 text-[15px] font-bold tabular-nums text-accent ring-1 ring-inset ring-accent/20" x-text="previewPrice || '₱0'"></p>
                </div>
            </div>

            {{-- Modal footer --}}
            <div class="flex shrink-0 items-center justify-end gap-2 border-t border-line bg-line/30 px-5 py-3.5">
                <button type="button" @click="showPreviewFlag = false" class="h-10 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40">
                    Close preview
                </button>
            </div>
        </div>
    </div>
</div>
