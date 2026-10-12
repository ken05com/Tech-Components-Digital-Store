<x-layouts.storefront>

    <div class="mx-auto w-full max-w-[820px] px-4 py-10 sm:px-6 lg:px-10">

        <div class="rounded-2xl border border-line-soft bg-white p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-brand/10 text-brand">
                    <x-storefront.icon name="check" class="h-6 w-6" />
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand">Order confirmed</p>
                    <h1 class="mt-1 text-[22px] font-extrabold tracking-tight text-ink">Thank you, {{ $sale->customerName }}!</h1>
                    <p class="mt-1 text-[13px] text-muted">Your order <span class="font-semibold text-ink">{{ $sale->reference }}</span> has been received.</p>
                </div>
            </div>

            <dl class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-line-soft bg-surface px-4 py-3">
                    <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Reference</dt>
                    <dd class="mt-1 text-[13.5px] font-semibold text-ink">{{ $sale->reference }}</dd>
                </div>
                <div class="rounded-xl border border-line-soft bg-surface px-4 py-3">
                    <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Placed</dt>
                    <dd class="mt-1 text-[13.5px] font-semibold text-ink">{{ $sale->orderDate() }}</dd>
                </div>
                <div class="rounded-xl border border-line-soft bg-surface px-4 py-3">
                    <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-muted">Total</dt>
                    <dd class="mt-1 text-[13.5px] font-extrabold tabular-nums text-brand">{{ $sale->formattedTotal() }}</dd>
                </div>
            </dl>

            <div class="mt-6">
                <h2 class="text-[13px] font-bold uppercase tracking-[0.12em] text-muted">Items</h2>
                <ul class="mt-3 divide-y divide-line-soft">
                    @foreach ($sale->items as $item)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="text-[13.5px] font-semibold text-ink">{{ $item->productName }}</p>
                                <p class="text-[11.5px] text-muted">{{ $item->quantity }} × {{ $item->formattedUnitPrice() }}</p>
                            </div>
                            <p class="text-[13.5px] font-bold tabular-nums text-ink">{{ $item->formattedSubtotal() }}</p>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-2 flex items-center justify-between border-t border-line-soft pt-4">
                    <span class="text-[13px] font-bold text-ink">Total paid</span>
                    <span class="text-[18px] font-extrabold tabular-nums text-brand">{{ $sale->formattedTotal() }}</span>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex h-11 items-center rounded-full bg-brand px-6 text-[13px] font-bold text-white transition hover:bg-brand-dark">
                    Continue shopping
                </a>
                <a href="{{ route('home') }}" class="inline-flex h-11 items-center rounded-full border border-line-soft px-6 text-[13px] font-bold text-ink transition hover:border-brand hover:text-brand">
                    Back to home
                </a>
            </div>
        </div>
    </div>

</x-layouts.storefront>
