@props(['product'])

<a href="{{ route('products.show', $product->id) }}" class="group flex flex-col gap-3 rounded-2xl border border-line bg-panel p-3.5 transition-all duration-200 hover:-translate-y-0.5 hover:border-accent/30 hover:bg-panel-raised">
    <div class="relative overflow-hidden rounded-xl border border-line bg-shell/70">
        <div class="grid h-32 place-items-center p-3">
            <img
                src="{{ $product->imageUrl() }}"
                alt="{{ $product->name }}"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
            >
        </div>
        <span class="absolute left-2 top-2 rounded-md bg-shell/85 px-1.5 py-0.5 text-[10px] font-bold tabular-nums text-slate-400 ring-1 ring-line">
            #{{ $product->displayId() }}
        </span>
    </div>

    <div class="min-w-0">
        <p class="truncate text-[13px] font-semibold text-slate-100 transition group-hover:text-accent">{{ $product->name }}</p>
        <p class="mt-1 line-clamp-2 text-[11.5px] leading-relaxed text-slate-500">{{ $product->description }}</p>
        <p class="mt-2 text-[12.5px] font-bold tabular-nums text-accent">{{ $product->priceLabel() }}</p>
    </div>
</a>
