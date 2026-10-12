<section class="mx-auto w-full max-w-[1440px] px-4 pt-14 sm:px-6 lg:px-10">
    <div class="flex items-end justify-between gap-4">
        <div>
            <h2 class="text-[22px] font-extrabold tracking-tight text-ink sm:text-[26px]">Ideas for Your Next Upgrade</h2>
            <p class="mt-1 text-[12.5px] text-muted">Inspiration for smarter setups, travel and play.</p>
        </div>
        <a href="{{ route('ideas') }}" class="group inline-flex shrink-0 items-center gap-1.5 text-[12.5px] font-bold text-brand transition hover:gap-2.5">
            See more ideas
            <x-storefront.icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
        </a>
    </div>

    @php
        $techVideos = [
            ['title' => 'Ultimate Desk Setup', 'description' => 'Immersive lighting and ergonomic dual-monitor workstation.', 'src' => asset('videos/TechSetup.mp4')],
            ['title' => 'RGB Battlestation & Gaming Gear', 'description' => 'Clean cable management and high-performance custom loop.', 'src' => asset('videos/TechSetup2.mp4')],
            ['title' => 'Minimalist Workspace', 'description' => 'Sleek aluminum peripherals and ambient vertical monitors.', 'src' => asset('videos/TechSetup3.mp4')],
        ];
    @endphp

    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($techVideos as $video)
            <div class="group relative overflow-hidden rounded-2xl border border-line-soft bg-surface shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="relative aspect-[16/10] w-full overflow-hidden bg-ink/10">
                    <video 
                        autoplay 
                        muted 
                        loop 
                        playsinline 
                        class="h-full w-full object-cover"
                    >
                        <source src="{{ $video['src'] }}" type="video/mp4">
                    </video>
                </div>
                <div class="p-5">
                    <h3 class="text-[15px] font-bold text-ink group-hover:text-brand transition-colors">{{ $video['title'] }}</h3>
                    <p class="mt-1 text-[12.5px] text-muted">{{ $video['description'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
