@php
    $benefits = [
        ['title' => 'Worldwide Shipping', 'description' => 'Fast delivery to US, UK & UAE', 'icon' => 'truck'],
        ['title' => 'Secure Payments', 'description' => '100% safe & secure checkout', 'icon' => 'shield-check'],
        ['title' => '30-Day Returns', 'description' => 'Hassle-free returns guaranteed', 'icon' => 'refresh'],
        ['title' => 'Premium Support', 'description' => '24/7 customer support', 'icon' => 'headset'],
    ];
@endphp

<section class="mt-14 w-full bg-surface" aria-label="Store benefits">
    <div class="mx-auto grid w-full max-w-[1440px] grid-cols-1 gap-6 px-4 py-10 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-10">
        @foreach ($benefits as $benefit)
            <div class="flex items-center gap-3.5">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand/[0.07] text-brand">
                    <x-storefront.icon :name="$benefit['icon']" class="h-5.5 w-5.5" />
                </span>
                <div>
                    <h3 class="text-[14px] font-extrabold tracking-tight text-ink">{{ $benefit['title'] }}</h3>
                    <p class="mt-0.5 text-[12px] text-muted">{{ $benefit['description'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>