<footer class="border-t border-line-soft bg-white">
    <div class="mx-auto w-full max-w-[1440px] px-4 py-12 sm:px-6 lg:px-10">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Brand & Socials --}}
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2.5" aria-label="Tech Components — home">
                    <img src="{{ asset('images/logo.png') }}" alt="Tech Components logo" class="h-8 w-8 object-contain rounded-lg">
                    <span class="text-[15px] font-extrabold tracking-tight text-ink">Tech Components</span>
                </a>
                <p class="mt-3 max-w-xs text-[12.5px] leading-relaxed text-muted">
                    Discover. Shop. Upgrade. The premier destination for cutting-edge tech gadgets, components, and accessories.
                </p>
                <div class="mt-4">
                    <p class="text-[11.5px] font-extrabold uppercase tracking-wider text-ink">Follow us</p>
                    <div class="mt-2 flex items-center gap-2">
                        @foreach ([['f', 'Facebook'], ['in', 'Instagram'], ['X', 'X'], ['YT', 'YouTube'], ['TK', 'TikTok']] as [$monogram, $service])
                            <span
                                title="{{ $service }} — official link"
                                class="grid h-9 w-9 cursor-pointer place-items-center rounded-full border border-line-soft bg-surface text-[12px] font-extrabold text-muted transition hover:border-brand hover:bg-brand hover:text-white"
                                aria-label="{{ $service }}"
                            >{{ $monogram }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Trust indicators --}}
            <div>
                <h2 class="text-[12px] font-extrabold uppercase tracking-[0.14em] text-ink">Trust &amp; Quality</h2>
                <ul class="mt-3 space-y-2.5 text-[12.5px] text-muted">
                    <li><span class="font-semibold text-ink">100% Authentic.</span> Genuine products only.</li>
                    <li><span class="font-semibold text-ink">Trusted by Thousands.</span> Loved by customers worldwide.</li>
                    <li><span class="font-semibold text-ink">Top Rated Store.</span> High-quality gadgets you can trust.</li>
                    <li><span class="font-semibold text-ink">Price Match Guarantee.</span> Best prices, always.</li>
                </ul>
            </div>

            {{-- Browse --}}
            <div>
                <h2 class="text-[12px] font-extrabold uppercase tracking-[0.14em] text-ink">Browse</h2>
                <ul class="mt-3 space-y-2.5 text-[12.5px]">
                    <li><a href="{{ route('products.index') }}" class="text-muted transition hover:text-brand">All products</a></li>
                    <li><a href="{{ route('categories.show', 'computer-components') }}" class="text-muted transition hover:text-brand">Computer Components</a></li>
                    <li><a href="{{ route('categories.show', 'storage-devices') }}" class="text-muted transition hover:text-brand">Storage Devices</a></li>
                    <li><a href="{{ route('categories.show', 'computer-accessories') }}" class="text-muted transition hover:text-brand">Computer Accessories</a></li>
                    <li><a href="{{ route('categories.show', 'networking-components') }}" class="text-muted transition hover:text-brand">Networking Components</a></li>
                    <li><a href="{{ route('categories.show', 'power-connection') }}" class="text-muted transition hover:text-brand">Power &amp; Connection</a></li>
                </ul>
            </div>

            {{-- Extras & Admin --}}
            <div>
                <h2 class="text-[12px] font-extrabold uppercase tracking-[0.14em] text-ink">More</h2>
                <ul class="mt-3 space-y-2.5 text-[12.5px]">
                    <li><a href="{{ route('ideas') }}" class="text-muted transition hover:text-brand">Ideas for your next upgrade</a></li>
                    <li><a href="{{ route('store.today') }}" class="text-muted transition hover:text-brand">Today</a></li>
                    <li><a href="{{ route('store.following') }}" class="text-muted transition hover:text-brand">Following</a></li>
                    <li><a href="{{ route('dashboard') }}" class="text-muted transition hover:text-brand">Admin dashboard</a></li>
                    <li><a href="{{ route('login') }}" class="text-muted transition hover:text-brand">Sign in</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-start justify-between gap-2 border-t border-line-soft pt-5 text-[11.5px] text-muted sm:flex-row sm:items-center">
            <p>&copy; {{ now()->year }} Tech Components — Discover. Shop. Upgrade.</p>
            <p>All rights reserved. Designed for elite tech enthusiasts.</p>
        </div>
    </div>
</footer>
