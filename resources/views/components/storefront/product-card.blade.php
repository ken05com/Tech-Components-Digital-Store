@props(['product'])

<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-line-soft bg-white transition hover:-translate-y-0.5 hover:border-brand/40 hover:shadow-[0_22px_46px_-24px_rgba(201,0,22,0.28)]">

    {{-- Picture --}}
    <a href="{{ route('products.detail', $product->id) }}" class="relative block aspect-square overflow-hidden bg-surface" aria-label="{{ $product->name }}">
        <img
            src="{{ $product->imageUrl() }}"
            alt="{{ $product->name }}"
            loading="lazy"
            class="h-full w-full object-contain p-4 transition duration-300 group-hover:scale-[1.04]"
            onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
        >
        <span class="absolute left-2.5 top-2.5 rounded-full border border-line-soft bg-white/95 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.08em] text-muted">
            {{ $product->category->shortLabel() }}
        </span>
    </a>

    {{-- Favorite --}}
    <button
        type="button"
        x-on:click="$store.storefront.toggle({ id: {{ $product->id }}, name: @js($product->name), url: @js(route('products.detail', $product->id)), image: @js($product->imageUrl()) })"
        :aria-pressed="$store.storefront.has({{ $product->id }})"
        aria-label="Save {{ $product->name }} to favorites"
        class="absolute right-2.5 top-2.5 grid h-9 w-9 place-items-center rounded-full border border-line-soft bg-white text-muted transition hover:text-brand"
    >
        <template x-if="$store.storefront.has({{ $product->id }})">
            <x-storefront.icon name="heart" filled class="h-[18px] w-[18px]" />
        </template>
        <template x-if="!$store.storefront.has({{ $product->id }})">
            <x-storefront.icon name="heart" class="h-[18px] w-[18px]" />
        </template>
    </button>

    {{-- Body --}}
    <div class="flex flex-1 flex-col gap-1.5 p-3.5">
        <a
            href="{{ route('products.detail', $product->id) }}"
            class="line-clamp-2 min-h-[2.35rem] text-[12.5px] font-semibold leading-snug text-ink transition hover:text-brand"
        >{{ $product->name }}</a>

        <p class="mt-auto text-[14px] font-extrabold tracking-tight text-brand">{{ $product->priceLabel() }}</p>

        @php
            $canManage = auth()->user()?->can('manage-products');
        @endphp
        @if ($product->stock > 0 && !$canManage)
            <a href="{{ route('products.detail', $product->id) }}" class="mt-1 inline-flex h-9 w-full items-center justify-center gap-1.5 rounded-lg bg-brand/10 text-[11.5px] font-bold uppercase tracking-[0.08em] text-brand transition hover:bg-brand hover:text-white">
                <x-storefront.icon name="cart" class="h-4 w-4" />
                Add to cart
            </a>
        @elseif ($product->stock > 0 && $canManage)
            <span class="mt-1 inline-flex h-9 w-full items-center justify-center rounded-lg bg-surface text-[11.5px] font-bold uppercase tracking-[0.08em] text-muted cursor-not-allowed">
                Admin Mode
            </span>
        @else
            <span class="mt-1 inline-flex h-9 w-full items-center justify-center rounded-lg bg-surface text-[11.5px] font-bold uppercase tracking-[0.08em] text-muted">
                Out of stock
            </span>
        @endif

        <a
            href="{{ route('products.detail', $product->id) }}"
            class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-[0.1em] text-muted transition hover:text-brand"
        >
            View details
            <x-storefront.icon name="arrow-right" class="h-3.5 w-3.5" />
        </a>
    </div>
</article>