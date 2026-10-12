<x-layouts.app active="inventory.index" :page-title="$pageTitle">
    <x-app.page-header
        :title="$pageTitle"
        eyebrow="Management"
        description="Monitor available stock, low inventory, and estimated stock value across the catalog."
    />

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Products" :value="$products->count()" hint="catalog items" icon="layers" />
        <x-ui.stat-card label="Units in Stock" :value="number_format($totalStock)" hint="across all products" icon="layers" active />
        <x-ui.stat-card label="Low Stock" :value="$lowStockCount" :hint="'At or below '.$threshold.' units'" icon="info" />
        <x-ui.stat-card label="Out of Stock" :value="$outOfStockCount" hint="need replenishment" icon="tag" accent />
    </div>

    <section class="overflow-hidden rounded-2xl border border-line bg-panel">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-3">
            <div>
                <h2 class="text-[13px] font-semibold text-ink">Stock by Product</h2>
                <p class="mt-0.5 text-[11px] text-muted">Estimated inventory value: ₱{{ number_format($stockValue, 2) }}</p>
            </div>
            <a href="{{ route('inventory.products') }}" class="inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface-strong px-3 text-[12px] font-semibold text-ink transition hover:border-accent/40 hover:text-accent">
                <x-app.icon name="layers" />
                View Products
            </a>
        </div>

        @if ($products->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-[14px] font-semibold text-ink">No products to track</p>
                <p class="mt-1 text-[12.5px] text-muted">Products will appear here once they are added to the catalog.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px] border-collapse text-left">
                    <caption class="sr-only">Inventory levels and prices for each product</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Product</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Category</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Price</th>
                            <th scope="col" class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Quantity</th>
                            <th scope="col" class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($products as $product)
                            <tr class="transition-colors hover:bg-line/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-[13px] font-semibold text-ink transition hover:text-accent">{{ $product->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-[12.5px] text-muted">{{ $product->category->label() }}</td>
                                <td class="px-4 py-3 text-[12.5px] font-medium tabular-nums text-ink">{{ $product->priceLabel() }}</td>
                                <td class="px-4 py-3 text-right text-[13px] font-semibold tabular-nums text-ink">{{ number_format($product->stock) }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($product->stock === 0)
                                        <span class="inline-flex items-center rounded-lg bg-accent/10 px-2.5 py-1 text-[11px] font-semibold text-accent">Out of stock</span>
                                    @elseif ($product->stock <= $threshold)
                                        <span class="inline-flex items-center rounded-lg bg-amber-400/10 px-2.5 py-1 text-[11px] font-semibold text-amber-300">Low stock</span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-emerald-400/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-300">In stock</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>