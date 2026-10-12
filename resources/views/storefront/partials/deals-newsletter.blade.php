<section class="mx-auto w-full max-w-[1440px] px-4 pt-14 sm:px-6 lg:px-10">
    <div class="grid gap-4 md:grid-cols-2">

        {{-- Exclusive deals --}}
        <div class="relative overflow-hidden rounded-3xl bg-brand p-8 text-white sm:p-10">
            <span class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"></span>
            <span class="pointer-events-none absolute -bottom-20 -left-10 h-56 w-56 rounded-full bg-white/10"></span>

            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15">
                <x-storefront.icon name="gift" class="h-6 w-6" />
            </span>

            <h2 class="mt-5 text-[24px] font-extrabold leading-tight tracking-tight sm:text-[28px]">Exclusive Deals for You!</h2>
            <p class="mt-2 max-w-sm text-[13.5px] leading-relaxed text-white/80">
                Save more on top-rated gadgets handpicked just for you.
            </p>
            <p class="mt-3 text-[11.5px] font-medium text-white/60">
                Demo state — no artificial discounts are applied; this opens the full catalog.
            </p>

            <a href="{{ route('products.index') }}" class="group mt-7 inline-flex h-11 items-center gap-2 rounded-full bg-white px-6 text-[13.5px] font-bold text-brand transition hover:bg-brand-dark hover:text-white">
                Shop Deals
                <x-storefront.icon name="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
            </a>
        </div>

        {{-- Newsletter --}}
        <div x-data="{ email: '', sent: false, error: '' }" class="relative overflow-hidden rounded-3xl bg-[#f2f2f4] p-8 sm:p-10">
            <x-storefront.icon name="mail" class="pointer-events-none absolute -bottom-6 -right-6 h-40 w-40 text-ink/[0.06]" />

            <h2 class="text-[24px] font-extrabold leading-tight tracking-tight text-ink sm:text-[28px]">Get the Latest Tech &amp; Deals</h2>
            <p class="mt-2 max-w-sm text-[13.5px] leading-relaxed text-muted">
                Join our newsletter and never miss an update.
            </p>

            <form
                class="relative z-10 mt-7"
                x-on:submit.prevent="error = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim()) ? '' : 'Please enter a valid email address.'; if (!error) { sent = true; email = '' }"
                novalidate
            >
                <label for="newsletter-email" class="sr-only">Email address</label>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input
                        id="newsletter-email"
                        name="email"
                        type="email"
                        x-model="email"
                        :aria-invalid="error ? 'true' : 'false'"
                        placeholder="you@example.com"
                        class="h-12 flex-1 rounded-full border border-line-soft bg-white px-5 text-[13px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                    >
                    <button type="submit" class="inline-flex h-12 items-center justify-center gap-1.5 rounded-full bg-brand px-6 text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                        Subscribe
                        <x-storefront.icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </div>

                <p x-show="error" x-text="error" class="mt-2 text-[12px] font-semibold text-brand"></p>
                <p
                    x-cloak
                    x-show="sent && !error"
                    class="mt-2 rounded-xl border border-brand/20 bg-brand/[0.06] px-3 py-2 text-[12px] font-semibold text-brand"
                    role="status"
                >Thanks! You're on the demo list. (Nothing is really subscribed or stored.)</p>
            </form>

            <p class="mt-4 text-[11px] text-muted">
                Demo — a newsletter backend is not wired up yet, so no email is sent or stored.
            </p>
        </div>
    </div>
</section>