@props(['product', 'size' => 'md'])

@php
    $box = match ($size) {
        'xs' => 'h-11 w-11',
        'sm' => 'h-14 w-14',
        'lg' => 'h-40 w-full',
        'xl' => 'h-56 w-full',
        default => 'h-16 w-16',
    };
@endphp

<span class="group/thumb relative grid shrink-0 place-items-center overflow-hidden rounded-xl border border-line bg-shell/80 {{ $box }}">
    <img
        src="{{ $product->imageUrl() }}"
        alt="{{ $product->name }}"
        loading="lazy"
        decoding="async"
        class="h-full w-full object-contain p-1.5 transition-transform duration-300 group-hover/thumb:scale-105"
        onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
    >
</span>
