<x-layouts.app active="sales.index" :page-title="$pageTitle">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
                    <a href="{{ route('purchases.index') }}" class="transition hover:text-accent">My Purchase</a>
                    <span class="text-muted">/</span>
                    <span class="text-muted">{{ $sale->reference }}</span>
                </nav>
                <h1 class="mt-1 text-[22px] font-bold tracking-tight text-ink sm:text-2xl">Order {{ $sale->reference }}</h1>
            </div>
            <a href="{{ route('purchases.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border border-line bg-panel px-4 text-[13px] font-semibold text-ink transition hover:bg-line/40">
                &larr; Back to purchases
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-line bg-white p-6 shadow-sm lg:col-span-2">
                <h2 class="text-[14px] font-semibold text-ink">Order Items</h2>
                <div class="mt-4 divide-y divide-line">
                    @foreach ($sale->items as $item)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-[13.5px] font-semibold text-ink">{{ $item->productName }}</p>
                                <p class="text-[12px] text-muted">Qty: {{ $item->quantity }} &times; ${{ number_format($item->unitPrice, 2) }}</p>
                            </div>
                            <span class="text-[13.5px] font-bold tabular-nums text-ink">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <hr class="my-5 border-line">
                <div class="flex items-center justify-between text-[14px] font-bold text-ink">
                    <span>Total Amount</span>
                    <span class="text-[18px] text-accent">${{ number_format($sale->total, 2) }}</span>
                </div>
            </div>

            <div class="rounded-2xl border border-line bg-white p-6 shadow-sm space-y-4">
                <h2 class="text-[14px] font-semibold text-ink">Customer Details</h2>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Name</p>
                    <p class="mt-0.5 text-[13.5px] font-medium text-ink">{{ $sale->customerName }}</p>
                </div>
                @if ($sale->customerEmail)
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Email</p>
                        <p class="mt-0.5 text-[13.5px] font-medium text-ink">{{ $sale->customerEmail }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Date Placed</p>
                    <p class="mt-0.5 text-[13.5px] font-medium text-ink">{{ $sale->createdAt ? \Illuminate\Support\Carbon::parse($sale->createdAt)->format('M d, Y H:i') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
