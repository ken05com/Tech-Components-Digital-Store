@props([
    'product' => null,
    'categories',
    'selectedCategory',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Add Product',
])

@php $isEdit = $product !== null; @endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="productForm(@js($product?->imageUrl()))"
    class="grid gap-5 lg:grid-cols-3"
>
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    {{-- Left: the fields --}}
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="tag" />
                </span>
                <h2 class="text-[14px] font-semibold text-slate-100">Product Details</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $product?->name) }}"
                        required
                        maxlength="120"
                        placeholder="e.g. CPU / Processor"
                        @class([
                            'h-11 w-full rounded-xl border bg-shell/70 px-3.5 text-[13px] text-slate-100 placeholder:text-slate-600 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('name'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1.5 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Description</label>
                    <textarea
                        id="description"
                        x-ref="description"
                        name="description"
                        rows="3"
                        required
                        maxlength="255"
                        placeholder="Short, clear summary of the product."
                        @class([
                            'w-full resize-y rounded-xl border bg-shell/70 px-3.5 py-3 text-[13px] leading-relaxed text-slate-100 placeholder:text-slate-600 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('description'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('description'),
                        ])
                    >{{ old('description', $product?->description) }}</textarea>
                    <div class="mt-1.5 flex items-center justify-between">
                        @error('description') <p class="text-[11.5px] text-rose-400">{{ $message }}</p> @else <span></span> @enderror
                        <span class="text-[11px] text-slate-600" x-text="`${descriptionLength()}/255`"></span>
                    </div>
                </div>

                <div>
                    <label for="category" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Category</label>
                    <select
                        id="category"
                        name="category"
                        required
                        @class([
                            'h-11 w-full appearance-none rounded-xl border bg-shell/70 px-3.5 text-[13px] text-slate-100 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('category'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('category'),
                        ])
                    >
                        @foreach ($categories as $option)
                            <option value="{{ $option->value }}" @selected(old('category', $selectedCategory->value) === $option->value)>{{ $option->label() }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1.5 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                        ID <span class="normal-case tracking-normal text-slate-600">(row number inside the category)</span>
                    </label>
                    <input
                        id="category_id"
                        name="category_id"
                        type="number"
                        min="1"
                        max="99"
                        value="{{ old('category_id', $product?->categoryId) }}"
                        @class([
                            'h-11 w-full rounded-xl border bg-shell/70 px-3.5 text-[13px] tabular-nums text-slate-100 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('category_id'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('category_id'),
                        ])
                    >
                    @error('category_id') <p class="mt-1.5 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[14px] font-semibold text-slate-100">Pricing</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="price_min" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Starting Price (₱)</label>
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
                            'h-11 w-full rounded-xl border bg-shell/70 px-3.5 text-[13px] tabular-nums text-slate-100 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('price_min'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('price_min'),
                        ])
                    >
                    @error('price_min') <p class="mt-1.5 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price_max" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">Maximum Price (₱)</label>
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
                            'h-11 w-full rounded-xl border bg-shell/70 px-3.5 text-[13px] tabular-nums text-slate-100 transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('price_max'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('price_max'),
                        ])
                    >
                    @error('price_max') <p class="mt-1.5 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 rounded-xl border border-line bg-shell/50 px-3.5 py-2.5">
                <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Preview</span>
                <span class="rounded-lg bg-accent/[0.08] px-2.5 py-1 text-[12.5px] font-semibold tabular-nums text-accent ring-1 ring-inset ring-accent/20" x-text="priceLabel()">₱0</span>
            </div>
        </div>
    </div>

    {{-- Right: image + actions --}}
    <div class="space-y-5">
        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="eye" />
                </span>
                <h2 class="text-[14px] font-semibold text-slate-100">Picture</h2>
            </div>

            <label
                for="image"
                class="group relative flex cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-xl border border-dashed border-line bg-shell/60 px-4 py-7 text-center transition hover:border-accent/45 hover:bg-accent/[0.04]"
            >
                <template x-if="preview">
                    <img :src="preview" alt="Selected product image preview" class="max-h-40 w-full object-contain">
                </template>
                <template x-if="! preview">
                    <span class="flex flex-col items-center gap-2">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/[0.04] text-slate-500 ring-1 ring-line transition group-hover:text-accent">
                            <x-app.icon name="layers" />
                        </span>
                        <span class="text-[12.5px] font-semibold text-slate-300">Choose an image</span>
                        <span class="text-[11px] text-slate-600">JPG, PNG, WEBP or GIF &middot; max 2&nbsp;MB</span>
                    </span>
                </template>
                <input
                    id="image"
                    name="image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="sr-only"
                    x-on:change="pickImage($event)"
                >
            </label>

            @error('image') <p class="mt-2 text-[11.5px] text-rose-400">{{ $message }}</p> @enderror

            <p class="mt-3 text-[11px] leading-relaxed text-slate-500">
                Images are stored in <span class="text-slate-400">public/images/products</span>. Leave this empty on edit to keep the current picture.
            </p>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="flex flex-col gap-2">
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(249,115,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                    <x-app.icon name="plus" />
                    {{ $submitLabel }}
                </button>
                <a href="{{ $isEdit ? route('products.show', $product->id) : route('products.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-white/[0.05] px-4 text-[13px] font-semibold text-slate-300 ring-1 ring-line transition hover:bg-white/10 hover:text-white">
                    Cancel
                </a>
            </div>
        </div>

        @if ($isEdit)
            <div class="rounded-2xl border border-rose-500/20 bg-rose-500/[0.05] p-5">
                <h2 class="text-[13px] font-semibold text-rose-200">Danger zone</h2>
                <p class="mt-1.5 text-[11.5px] leading-relaxed text-rose-200/70">Removing this row only affects your local catalog copy.</p>
                <div class="mt-3.5">
                    <x-catalog.confirm-delete :product="$product" />
                </div>
            </div>
        @endif
    </div>
</form>
