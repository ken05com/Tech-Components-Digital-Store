@php
    $cards = [
        ['label' => 'Computer Components', 'tag' => 'Processors & motherboards', 'icon' => 'cpu', 'slug' => 'computer-components'],
        ['label' => 'Storage Devices', 'tag' => 'SSDs, HDDs & flash', 'icon' => 'database', 'slug' => 'storage-devices'],
        ['label' => 'Computer Accessories', 'tag' => 'Keyboards, mice & audio', 'icon' => 'headphones', 'slug' => 'computer-accessories'],
        ['label' => 'Networking Components', 'tag' => 'Routers & switches', 'icon' => 'router', 'slug' => 'networking-components'],
        ['label' => 'Power & Connection', 'tag' => 'Cables, hubs & power', 'icon' => 'usb', 'slug' => 'power-connection'],
    ];
@endphp

<hr class="my-6 border-line-soft">

<section class="mx-auto w-full max-w-[1440px] px-4 sm:px-6 lg:px-10">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
        @foreach ($cards as $card)
            <a
                href="{{ route('categories.show', $card['slug']) }}"
                title="Browse {{ $card['label'] }}"
                class="group relative overflow-hidden rounded-2xl border border-line-soft bg-white p-4 transition hover:-translate-y-0.5 hover:border-brand/40 hover:shadow-[0_18px_40px_-24px_rgba(201,0,22,0.35)]"
            >
                <div class="flex items-start justify-between">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand/10 text-brand transition group-hover:bg-brand group-hover:text-white">
                        <x-storefront.icon :name="$card['icon']" class="h-5 w-5" />
                    </span>
                    <span class="grid h-7 w-7 place-items-center rounded-full bg-surface text-muted transition group-hover:bg-brand group-hover:text-white">
                        <x-storefront.icon name="arrow-up-right" class="h-4 w-4" />
                    </span>
                </div>

                <h3 class="mt-3 text-[14.5px] font-extrabold tracking-tight text-ink">{{ $card['label'] }}</h3>
                <p class="mt-0.5 text-[11.5px] font-medium text-muted">{{ $card['tag'] }}</p>
            </a>
        @endforeach
    </div>
</section>
