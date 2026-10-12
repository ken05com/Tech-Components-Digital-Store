<x-layouts.app active="sales.index" :page-title="$pageTitle">
    <x-app.page-header
        :title="$pageTitle"
        eyebrow="Account"
        :description="'Review your purchase history and order statuses. ' . $totalSales . ' orders placed totaling $' . number_format($revenue, 2) . '.' "
    />

    {{-- Summary cards --}}
    <div class="grid gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="Total Orders" :value="$totalSales" hint="completed purchases" icon="tag" active />
        <x-ui.stat-card label="Items Purchased" :value="$itemsSold" hint="hardware items" icon="layers" />
        <x-ui.stat-card label="Total Spent" :value="'$' . number_format($revenue, 2)" hint="lifetime order value" icon="cart" />
    </div>

    {{-- Sales / Purchases table --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-panel shadow-[0_1px_0_0_rgba(255,255,255,0.03)_inset]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="tag" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Order History</h2>
            </div>
            <span class="rounded-lg bg-surface-strong px-2 py-1 text-[11px] text-muted ring-1 ring-line">
                <span class="font-semibold text-ink">{{ $totalSales }}</span> orders
            </span>
        </div>

        @if ($sales->isEmpty())
            <div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
                <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-surface-strong text-muted">
                    <x-app.icon name="tag" />
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-ink">No purchases yet</p>
                    <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-muted">
                        When you purchase computer components from the catalog, your order records will appear here.
                    </p>
                </div>
                <a href="{{ route('products.index') }}" class="mt-1 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white transition hover:bg-accent-soft">
                    <x-app.icon name="cart" />
                    Browse Products
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] border-collapse text-left">
                    <caption class="sr-only">Order history with references, item counts, totals, and view actions</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">REFERENCE</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">CUSTOMER</th>
                            <th scope="col" class="w-32 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ITEMS</th>
                            <th scope="col" class="w-36 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">TOTAL</th>
                            <th scope="col" class="w-40 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">DATE</th>
                            <th scope="col" class="w-32 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($sales as $record)
                            <tr class="group transition-colors duration-150 hover:bg-line/40">
                                <td class="px-4 py-3 align-middle">
                                    <a href="{{ route('sales.show', $record->id) }}" class="font-mono font-bold text-accent transition hover:underline">
                                        {{ $record->reference }}
                                    </a>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="text-[13.5px] font-semibold text-ink">{{ $record->customerName }}</span>
                                    @if ($record->customerEmail)
                                        <span class="block text-[11.5px] text-muted">{{ $record->customerEmail }}</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-flex items-center rounded-lg bg-surface-strong px-2.5 py-1 text-[12px] font-semibold tabular-nums text-ink ring-1 ring-line">
                                        {{ $record->itemsCount }} {{ $record->itemsCount === 1 ? 'item' : 'items' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="text-[13.5px] font-bold tabular-nums text-ink">${{ number_format($record->total, 2) }}</span>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="text-[12.5px] text-muted">{{ $record->createdAt ? \Illuminate\Support\Carbon::parse($record->createdAt)->format('M d, Y H:i') : '—' }}</span>
                                </td>

                                <td class="px-4 py-3 text-right align-middle">
                                    <a href="{{ route('sales.show', $record->id) }}" class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-line bg-surface-strong px-3 text-[12px] font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
                                        <x-app.icon name="eye" />
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
