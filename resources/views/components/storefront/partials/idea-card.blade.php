@props(['idea', 'slug'])

<a
    href="{{ route('ideas.show', $slug) }}"
    class="group relative flex h-56 flex-col justify-end overflow-hidden rounded-2xl transition hover:-translate-y-0.5 hover:shadow-[0_24px_50px_-22px_rgba(0,0,0,0.35)] sm:h-64"
    aria-label="{{ $idea['title'] }} — {{ $idea['count'] }} ideas"
>
    {{-- Placeholder illustration (gradient + icon, no external images) --}}
    <span class="absolute inset-0 bg-gradient-to-br {{ $idea['bg'] }}"></span>
    <span class="absolute inset-0 grid place-items-center">
        <x-storefront.icon :name="$idea['icon']" class="h-16 w-16 text-white/80" />
    </span>
    <span class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></span>

    {{-- Arrow --}}
    <span class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-full bg-white text-ink transition group-hover:bg-brand group-hover:text-white">
        <x-storefront.icon name="arrow-up-right" class="h-4 w-4" />
    </span>

    {{-- Copy --}}
    <span class="relative z-10 p-4">
        <span class="block text-[12px] font-bold text-white/80">{{ number_format($idea['count']) }} Ideas</span>
        <span class="mt-0.5 block text-[16px] font-extrabold leading-tight tracking-tight text-white">{{ $idea['title'] }}</span>
    </span>
</a>