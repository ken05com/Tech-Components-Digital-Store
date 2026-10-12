<style>
    .hx-layer { position:absolute; inset:0; overflow:hidden; pointer-events:none; }

    .hx-ring  { position:absolute; left:50%; top:50%; border-radius:9999px; transform:translate(-50%,-50%); }
    .hx-ring--1 { width:360px; height:360px; border:1px solid rgba(255,255,255,.22); }
    .hx-ring--2 { width:470px; height:470px; border:1px solid rgba(255,255,255,.12); }
    .hx-ring--3 { width:420px; height:420px; border:2px dashed rgba(255,255,255,.30); transform:translate(-50%,-50%) rotate(12deg); }
    .hx-ring--4 { width:250px; height:250px; border:10px solid rgba(255,255,255,.07); }
    .hx-glow  { position:absolute; left:50%; top:50%; width:300px; height:300px; border-radius:9999px; transform:translate(-50%,-50%); background:rgba(255,255,255,.14); filter:blur(24px); }

    .hx-dots  { position:absolute; background-image:radial-gradient(circle, rgba(255,255,255,.9) 1.5px, transparent 1.5px); background-size:12px 12px; }
    .hx-dot   { position:absolute; border-radius:9999px; }
    .hx-plus  { position:absolute; width:20px; height:20px; }
    .hx-plus::before, .hx-plus::after { content:""; position:absolute; background:#fff; border-radius:2px; }
    .hx-plus::before { left:50%; top:0; width:2px; height:100%; transform:translateX(-50%); }
    .hx-plus::after  { top:50%; left:0; height:2px; width:100%; transform:translateY(-50%); }
    .hx-bar   { position:absolute; height:12px; border-radius:9999px; transform:rotate(-35deg); }

    .hx-tri   { position:absolute; background:rgba(255,255,255,.14); clip-path:polygon(50% 0, 100% 100%, 0 100%); }
    .hx-hex   { position:absolute; background:rgba(255,255,255,.10); clip-path:polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0 50%); }
    .hx-star  { position:absolute; color:#fff; line-height:1; }
    .hx-half  { position:absolute; border-radius:9999px; border:3px solid rgba(255,255,255,.35); border-bottom-color:transparent; border-left-color:transparent; transform:rotate(-20deg); }

    .hx-badge { position:absolute; z-index:20; display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:9999px;
                background:#fff; color:var(--color-ink,#14141a); font-size:11px; font-weight:800; line-height:1; white-space:nowrap;
                box-shadow:0 16px 30px -14px rgba(100,0,10,.55); }
    .hx-badge--brand { color:var(--color-brand,#c90016); }
    .hx-badge--dark  { background:var(--color-ink,#14141a); color:#fff; box-shadow:0 16px 30px -14px rgba(0,0,0,.6); }
    .hx-badge--glass { background:rgba(120,0,12,.85); color:#fff; border:1px solid rgba(255,255,255,.3); text-transform:uppercase; letter-spacing:.14em; font-size:10px; font-weight:700; }
    .hx-round { position:absolute; z-index:20; display:grid; place-items:center; text-align:center; border-radius:9999px; }
    .hx-round--dark  { width:88px; height:88px; background:var(--color-ink,#14141a); color:#fff; box-shadow:0 20px 40px -16px rgba(0,0,0,.6); outline:4px solid rgba(255,255,255,.3); }
    .hx-round--white { width:60px; height:60px; background:#fff; color:var(--color-brand,#c90016); box-shadow:0 18px 34px -14px rgba(100,0,10,.55); }
    .hx-round small  { display:block; font-size:10px; font-weight:700; letter-spacing:.18em; text-transform:uppercase; opacity:.7; line-height:1; }
    .hx-round strong { display:block; font-size:24px; font-weight:800; line-height:1; }
    .hx-live { width:8px; height:8px; border-radius:9999px; background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.25); }

    @keyframes hx-float { 0%,100% { translate:0 0; } 50% { translate:0 -8px; } }
    .hx-float   { animation:hx-float 5s ease-in-out infinite; }
    .hx-float-2 { animation:hx-float 6.5s ease-in-out infinite reverse; }
    @media (prefers-reduced-motion: reduce) { .hx-float, .hx-float-2 { animation:none; } }
</style>

<section class="mx-auto w-full max-w-[1440px] px-4 pt-6 sm:px-6 lg:px-10">
    <div class="relative overflow-hidden rounded-[26px] bg-[#f2f2f4] sm:rounded-[32px]">

        <div class="relative z-10 grid lg:grid-cols-[1fr_560px]">
            <div class="px-6 py-10 sm:px-10 sm:py-14 lg:px-12 lg:py-16">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-brand/20 bg-white px-3 py-1.5 text-[10.5px] font-bold uppercase tracking-[0.2em] text-brand">
                    <x-storefront.icon name="sparkles" class="h-3.5 w-3.5" />
                    Discover. Shop. Upgrade.
                </span>

                <h1 class="mt-5 text-[40px] font-extrabold leading-[0.98] tracking-tight text-ink sm:text-[52px] lg:text-[58px]">
                    Latest Tech<br>
                    <span class="text-brand">Gadgets</span>
                </h1>

                <p class="mt-4 max-w-md text-[14.5px] leading-relaxed text-muted">
                    Explore cutting-edge gadgets that upgrade your lifestyle — components, audio, storage and accessories from the Tech Components catalog.
                </p>

                <div class="mt-7 flex flex-wrap items-center gap-3">
                    <a href="{{ route('products.index') }}" class="group inline-flex h-12 items-center gap-2 rounded-full bg-brand px-6 text-[14px] font-bold text-white shadow-[0_14px_30px_-12px_rgba(201,0,22,0.7)] transition hover:bg-brand-dark">
                        Shop Now
                        <x-storefront.icon name="arrow-right" class="h-4.5 w-4.5 transition-transform group-hover:translate-x-0.5" />
                    </a>
                    <a href="{{ route('categories.show', 'computer-components') }}" class="inline-flex h-12 items-center rounded-full border border-ink/10 bg-white px-6 text-[14px] font-bold text-ink transition hover:border-brand hover:text-brand">
                        Browse Collection
                    </a>
                </div>

                <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-2.5 text-[11.5px] font-semibold text-muted">
                    <span class="inline-flex items-center gap-1.5">
                        <x-storefront.icon name="truck" class="h-4 w-4 text-brand" />
                        Free shipping
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <x-storefront.icon name="shield-check" class="h-4 w-4 text-brand" />
                        Secure checkout
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <x-storefront.icon name="refresh" class="h-4 w-4 text-brand" />
                        30-day returns
                    </span>
                </div>
            </div>

            <div class="relative hidden lg:block">

                <div class="pointer-events-none absolute -left-14 top-1/2 h-[118%] w-[120%] -translate-y-1/2 rounded-[45%_55%_52%_48%/52%_48%_55%_45%] bg-brand"></div>
                <div class="pointer-events-none absolute -left-8 top-1/2 h-[120%] w-[110%] -translate-y-1/2 rotate-[-6deg] rounded-[48%_52%_50%_50%/50%_50%_48%_52%] bg-brand-dark opacity-60 blur-[2px]"></div>

                <div class="hx-layer" aria-hidden="true">
                    <div class="hx-glow"></div>
                    <div class="hx-ring hx-ring--1"></div>
                    <div class="hx-ring hx-ring--2"></div>
                    <div class="hx-ring hx-ring--3"></div>
                    <div class="hx-ring hx-ring--4"></div>

                    <div class="hx-dots" style="left:40px; top:32px; width:96px; height:96px; opacity:.4;"></div>
                    <div class="hx-dots" style="right:32px; bottom:40px; width:112px; height:80px; opacity:.3;"></div>
                    <div class="hx-dots" style="right:60px; top:150px; width:60px; height:60px; opacity:.25;"></div>

                    <div class="hx-dot" style="left:64px; top:33%; width:16px; height:16px; background:rgba(255,255,255,.7);"></div>
                    <div class="hx-dot" style="right:96px; top:96px; width:10px; height:10px; background:rgba(255,255,255,.6);"></div>
                    <div class="hx-dot" style="left:96px; bottom:96px; width:24px; height:24px; border:2px solid rgba(255,255,255,.5);"></div>
                    <div class="hx-dot" style="right:33%; bottom:64px; width:12px; height:12px; background:rgba(255,255,255,.5);"></div>
                    <div class="hx-dot" style="left:48%; top:20px; width:34px; height:34px; border:2px solid rgba(255,255,255,.35);"></div>
                    <div class="hx-dot" style="right:18px; top:46%; width:46px; height:46px; background:rgba(0,0,0,.12);"></div>

                    <div class="hx-plus" style="right:64px; top:50%; opacity:.7;"></div>
                    <div class="hx-plus" style="left:112px; bottom:48px; width:16px; height:16px; opacity:.6;"></div>
                    <div class="hx-plus" style="left:30%; top:56px; width:14px; height:14px; opacity:.55;"></div>

                    <div class="hx-tri"  style="left:24px; top:40%; width:44px; height:40px; transform:rotate(-14deg);"></div>
                    <div class="hx-tri"  style="right:120px; bottom:28px; width:30px; height:26px; transform:rotate(18deg); opacity:.8;"></div>
                    <div class="hx-hex"  style="right:36px; top:118px; width:58px; height:58px;"></div>
                    <div class="hx-hex"  style="left:150px; bottom:20px; width:40px; height:40px; opacity:.8;"></div>
                    <div class="hx-half" style="left:54px; top:64px; width:70px; height:70px;"></div>
                    <div class="hx-half" style="right:84px; bottom:70px; width:54px; height:54px; transform:rotate(150deg);"></div>
                    <div class="hx-star" style="left:42%; bottom:26px; font-size:20px; opacity:.8;">✦</div>
                    <div class="hx-star" style="right:44px; top:30%; font-size:14px; opacity:.7;">✦</div>
                    <div class="hx-star" style="left:20px; top:62%; font-size:12px; opacity:.6;">✦</div>

                    <div class="hx-bar" style="right:-24px; top:56px; width:160px; background:rgba(255,255,255,.15);"></div>
                    <div class="hx-bar" style="right:-8px; top:96px; width:96px; background:rgba(255,255,255,.10);"></div>
                    <div class="hx-bar" style="left:-16px; bottom:80px; width:144px; background:rgba(0,0,0,.10);"></div>
                    <div class="hx-bar" style="left:-30px; top:120px; width:110px; background:rgba(255,255,255,.08);"></div>
                </div>

                <div class="relative z-10 flex h-full min-h-[390px] items-center justify-center p-4">

                    <img
                        src="{{ asset('images/hero-showcase.png') }}"
                        alt="Tech Components Hardware Showcase"
                        class="relative z-10 h-auto w-full max-w-[530px] scale-[1.05] object-contain drop-shadow-[0_25px_35px_rgba(0,0,0,0.45)] transition-transform duration-500 hover:scale-[1.08]"
                    >

                    <span class="absolute right-2 top-2 z-20 grid h-20 w-20 -rotate-6 place-items-center rounded-full bg-white text-center shadow-[0_20px_40px_-16px_rgba(100,0,10,0.5)]">
                        <span class="text-[11px] font-extrabold uppercase leading-tight tracking-[0.16em] text-brand">
                            New<br>Arrivals
                        </span>
                    </span>

                    <span class="hx-badge hx-float" style="left:0; top:24px; transform:rotate(-3deg);">
                        <span style="color:#fbbf24; font-size:13px;">★</span>
                        4.9 Top Rated
                    </span>

                    <span class="hx-badge hx-badge--glass hx-float-2" style="left:38%; top:4px;">
                        <x-storefront.icon name="sparkles" class="h-3.5 w-3.5" />
                        Pro Pick
                    </span>

                    <span class="hx-badge hx-float-2" style="right:6px; top:108px; transform:rotate(3deg);">
                        <span class="hx-live"></span>
                        In Stock
                    </span>

                    <span class="hx-badge hx-badge--glass hx-float" style="left:14px; top:38%;">
                        <x-storefront.icon name="shield-check" class="h-3.5 w-3.5" />
                        Warranty
                    </span>

                    <span class="hx-round hx-round--dark hx-float-2" style="left:8px; bottom:16px; transform:rotate(6deg);">
                        <span>
                            <small>Up to</small>
                            <strong>30%</strong>
                            <small>Off</small>
                        </span>
                    </span>

                    <span class="hx-badge hx-badge--dark hx-float" style="left:120px; bottom:6px; transform:rotate(-2deg);">
                        <span style="color:#fbbf24;">🔥</span>
                        Best Seller
                    </span>

                    <span class="hx-round hx-round--white hx-float" style="right:22px; bottom:96px; transform:rotate(-8deg);">
                        <span style="font-size:11px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; line-height:1.1;">Hot<br>Deal</span>
                    </span>

                    <span class="hx-badge hx-badge--brand hx-float-2" style="right:0; bottom:40px; transform:rotate(2deg);">
                        <x-storefront.icon name="truck" class="h-4 w-4" />
                        Free Shipping
                    </span>

                </div>
            </div>
        </div>

        <div class="pointer-events-none absolute -bottom-24 -right-24 h-56 w-56 rounded-full bg-brand opacity-[0.06] lg:hidden"></div>
    </div>
</section>