<x-layouts.app active="deleted-items.index" :page-title="$pageTitle">
    <x-app.page-header
        title="Deleted Items"
        eyebrow="Utilities"
        :description="'Recoverable products that have been removed from the active catalog. '.$deletedCount.' in the bin.'"
    >
        <x-slot:actions>
            <a href="{{ route('products.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40 hover:text-ink">
                <x-app.icon name="layers" />
                Back to catalog
            </a>
        </x-slot:actions>
    </x-app.page-header>

    <div class="grid gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="In Deleted Items" :value="$deletedCount" hint="recoverable" icon="trash" active />
        <x-ui.stat-card label="Active Products" :value="$totalProducts" hint="in catalog" icon="layers" :href="route('products.index')" />
        <x-ui.stat-card label="Subtopics" :value="5" hint="fixed structure" icon="tag" />
    </div>

    <div class="overflow-hidden rounded-2xl border border-line bg-panel shadow-[0_1px_0_0_rgba(255,255,255,0.03)_inset]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-rose-500/12 text-rose-600 ring-1 ring-rose-500/20">
                    <x-app.icon name="trash" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Deleted Products</h2>
            </div>

            <form method="GET" action="{{ route('deleted-items.index') }}" class="relative w-full sm:w-72">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                    <x-app.icon name="search" />
                </span>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] }}"
                    placeholder="Search deleted items…"
                    aria-label="Search deleted items"
                    class="h-10 w-full rounded-xl border border-line bg-surface-strong pl-10 pr-3 text-[13px] text-ink placeholder:text-muted transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20"
                >
            </form>
        </div>

        @if ($products->total() === 0)
            <div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
                <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-surface-strong text-muted">
                    <x-app.icon name="trash" />
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-ink">No deleted items</p>
                    <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-muted">
                        @if ($filters['search'] !== '')
                            Nothing matches &ldquo;{{ $filters['search'] }}&rdquo;. Try a different keyword.
                        @else
                            Products you delete will appear here so they can be restored.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse text-left">
                    <caption class="sr-only">Deleted products with restore and permanent delete actions</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="w-20 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ID</th>
                            <th scope="col" class="w-56 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">NAME</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">DESCRIPTION</th>
                            <th scope="col" class="w-44 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PRICE</th>
                            <th scope="col" class="w-28 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PICTURE</th>
                            <th scope="col" class="w-40 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($products as $product)
                            <tr class="group transition-colors duration-150 hover:bg-line/40">
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-grid h-8 min-w-8 place-items-center rounded-lg bg-surface-strong px-2 text-[12px] font-bold tabular-nums text-ink ring-1 ring-line">{{ $product->displayId() }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="block text-[13.5px] font-semibold text-ink">{{ $product->name }}</span>
                                    <span class="mt-0.5 block text-[11px] text-muted">{{ $product->category->label() }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <p class="max-w-md text-[12.5px] leading-relaxed text-muted">{{ $product->description }}</p>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-flex items-center rounded-lg bg-accent/[0.08] px-2.5 py-1.5 text-[12.5px] font-semibold tabular-nums text-accent ring-1 ring-inset ring-accent/20">{{ $product->priceLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <x-catalog.product-thumbnail :product="$product" size="sm" />
                                </td>
                                <td class="px-4 py-3 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('restore-products')
                                            <a href="{{ route('deleted-items.restore.form', $product->id) }}" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-emerald-500/25 bg-emerald-500/[0.07] px-3 text-[12px] font-semibold text-emerald-600 transition hover:border-emerald-500/45 hover:bg-emerald-500/[0.12]">
                                                <x-app.icon name="refresh" />
                                                Restore
                                            </a>
                                        @endcan
                                        @can('force-delete')
                                            <x-app.confirm-form
                                                :action="route('deleted-items.destroy', $product->id)"
                                                method="DELETE"
                                                compact
                                                icon="trash"
                                                title="Permanently delete?"
                                                :message="'<span class=&quot;font-semibold text-ink&quot;>'.e($product->name).'</span> will be permanently removed. This cannot be undone.'"
                                                confirm-label="Delete forever"
                                            />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <x-catalog.pager :paginator="$products" />
            @endif
        @endif
    </div>
</x-layouts.app>
