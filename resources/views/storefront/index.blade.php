<x-layouts.storefront>

    @include('storefront.partials.hero')

    @include('storefront.partials.categories')

    {{-- Trending --}}
    @include('storefront.partials.trending', ['trending' => $trending, 'totalProducts' => $totalProducts])

    {{-- Store benefits --}}
    @include('storefront.partials.benefits')

    {{-- Editorial ideas --}}
    @include('storefront.partials.ideas', ['ideas' => $ideas])

    {{-- Deals + newsletter --}}
    @include('storefront.partials.deals-newsletter')

</x-layouts.storefront>