<x-layouts.storefront>

    <div class="mx-auto w-full max-w-[1200px] px-4 py-8 sm:px-6 lg:px-10">

        <div class="mb-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand">Secure checkout</p>
            <h1 class="mt-1 text-[24px] font-extrabold tracking-tight text-ink">Checkout</h1>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-500/30 bg-rose-500/[0.06] px-4 py-3 text-[13px] text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="grid gap-6 lg:grid-cols-[1fr_340px]">
            @csrf

            <div class="space-y-5">
                <div class="rounded-2xl border border-line-soft bg-white p-5">
                    <h2 class="text-[14px] font-bold text-ink">Contact details</h2>
                    <div class="mt-4 grid gap-4">
                        <div>
                            <label for="customer_name" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.12em] text-muted">Full name</label>
                            <input
                                id="customer_name"
                                name="customer_name"
                                type="text"
                                required
                                value="{{ old('customer_name', auth()->user()?->name) }}"
                                placeholder="Juan Dela Cruz"
                                class="h-11 w-full rounded-xl border border-line-soft bg-surface px-3.5 text-[13px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                            >
                            @error('customer_name') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="customer_email" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.12em] text-muted">Email <span class="normal-case tracking-normal text-muted">(optional)</span></label>
                            <input
                                id="customer_email"
                                name="customer_email"
                                type="email"
                                value="{{ old('customer_email', auth()->user()?->email) }}"
                                placeholder="you@example.com"
                                class="h-11 w-full rounded-xl border border-line-soft bg-surface px-3.5 text-[13px] text-ink placeholder:text-muted focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/10"
                            >
                            @error('customer_email') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-line-soft bg-white p-5">
                    <h2 class="text-[14px] font-bold text-ink">Items</h2>
                    <ul class="mt-4 divide-y divide-line-soft">
                        @foreach ($lines as $line)
                            <li class="flex items-center justify-between gap-3 py-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-lg border border-line-soft bg-surface">
                                        <img src="{{ $line['product']->imageUrl() }}" alt="{{ $line['product']->name }}" class="h-full w-full object-contain p-1.5" onerror="this.src='{{ asset('images/products/placeholder.svg') }}'">
                                    </span>
                                    <div class="min-w-0">
                                        <p class="line-clamp-1 text-[13px] font-semibold text-ink">{{ $line['product']->name }}</p>
                                        @if ($line['variant'])
                                            <p class="text-[11.5px] text-muted">Variant: <span class="font-semibold text-ink">{{ $line['variant'] }}</span></p>
                                        @endif
                                        <p class="text-[11.5px] text-muted">{{ $line['quantity'] }} × ₱{{ number_format($line['unit_price']) }}</p>
                                    </div>
                                </div>
                                <p class="text-[13px] font-bold tabular-nums text-ink">₱{{ number_format($line['subtotal']) }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <aside class="h-fit rounded-2xl border border-line-soft bg-surface p-5 lg:sticky lg:top-24">
                <h2 class="text-[14px] font-bold text-ink">Order summary</h2>
                <dl class="mt-4 space-y-2 text-[13px]">
                    <div class="flex items-center justify-between">
                        <dt class="text-muted">Items</dt>
                        <dd class="font-semibold tabular-nums text-ink">{{ collect($lines)->sum('quantity') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-muted">Subtotal</dt>
                        <dd class="font-semibold tabular-nums text-ink">₱{{ number_format($subtotal) }}</dd>
                    </div>
                </dl>
                <div class="mt-4 flex items-center justify-between border-t border-line-soft pt-4">
                    <span class="text-[13px] font-bold text-ink">Total</span>
                    <span class="text-[18px] font-extrabold tabular-nums text-brand">₱{{ number_format($subtotal) }}</span>
                </div>
                <button type="submit" class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-brand text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                    Place order
                    <x-storefront.icon name="arrow-right" class="h-4 w-4" />
                </button>
                <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-[12px] font-semibold text-muted transition hover:text-brand">Back to cart</a>
            </aside>
        </form>
    </div>

</x-layouts.storefront>
