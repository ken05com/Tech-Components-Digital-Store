@props([
    'paginator',
    'categories' => null,
    'filters' => [],
    'editable' => false,
    'deletable' => false,
    'baseRoute' => 'products.index',
    'baseParams' => [],
    'lockedCategory' => null,
    'searchParam' => 'search',
    'sortOptions' => [
        'name' => 'Name (A–Z)',
        'name-desc' => 'Name (Z–A)',
        'price-asc' => 'Price (low → high)',
        'price-desc' => 'Price (high → low)',
    ],
])

@php
    $currentSort = $filters['sort'] ?? 'default';
    $currentSearch = $filters['search'] ?? '';
    $currentCategory = $filters['category'] ?? 'all';
    $hasFilters = $currentSearch !== '' || ($currentCategory !== 'all' && $currentCategory !== $lockedCategory);
@endphp

<div class="space-y-4">

    {{-- Toolbar: search / filter / sort / add --}}
    <div class="flex flex-col gap-3 rounded-2xl border border-line bg-panel p-3.5 lg:flex-row lg:items-center">

        <form method="GET" action="{{ route($baseRoute, $baseParams) }}" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            @if ($lockedCategory)
                <input type="hidden" name="category" value="{{ $lockedCategory }}">
            @endif

            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                    <x-app.icon name="search" />
                </span>
                <input
                    type="search"
                    name="{{ $searchParam }}"
                    value="{{ $currentSearch }}"
                    placeholder="Search by name, description or category…"
                    aria-label="Search products"
                    class="h-10 w-full rounded-xl border border-line bg-surface-strong pl-10 pr-3 text-[13px] text-ink placeholder:text-muted transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20"
                >
            </div>

            @if ($categories)
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                        <x-app.icon name="filter" />
                    </span>
                    <select
                        name="category"
                        onchange="this.form.submit()"
                        aria-label="Filter by category"
                        class="h-10 w-full appearance-none rounded-xl border border-line bg-surface-strong pl-9 pr-8 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20 sm:w-52"
                    >
                        <option value="all" @selected($currentCategory === 'all')>All categories</option>
                        @foreach ($categories as $option)
                            <option value="{{ $option->value }}" @selected($currentCategory === $option->value)>{{ $option->label() }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-muted">
                        <x-app.icon name="chevron-right" />
                    </span>
                </div>
            @endif

            <div class="relative">
                <select
                    name="sort"
                    onchange="this.form.submit()"
                    aria-label="Sort products"
                    class="h-10 w-full appearance-none rounded-xl border border-line bg-surface-strong pl-3 pr-8 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20 sm:w-48"
                >
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected($currentSort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 rotate-90 text-muted">
                    <x-app.icon name="chevron-right" />
                </span>
            </div>

            <button type="submit" class="h-10 shrink-0 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40 hover:text-ink">
                Apply
            </button>

            @if ($hasFilters)
                <a href="{{ route($baseRoute, $baseParams) }}" class="h-10 shrink-0 rounded-xl px-3 text-center text-[13px] font-medium leading-10 text-muted transition hover:text-accent">
                    Clear
                </a>
            @endif
        </form>

        @if ($editable)
            <div class="flex shrink-0 items-center gap-2 border-t border-line pt-3 lg:border-0 lg:pt-0">
                <a
                    href="{{ route('products.create', $lockedCategory ? ['category' => $lockedCategory] : []) }}"
                    class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(201,0,22,0.8)] transition hover:bg-accent-soft active:scale-[0.98] lg:flex-none"
                >
                    <x-app.icon name="plus" />
                    Add Product
                </a>
            </div>
        @endif
    </div>

    {{-- Table panel --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-panel shadow-[0_1px_0_0_rgba(255,255,255,0.03)_inset]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Product Overview</h2>
            </div>
            <div class="flex items-center gap-2 text-[11px] text-muted">
                <span class="rounded-lg bg-surface-strong px-2 py-1 ring-1 ring-line">
                    Showing <span class="font-semibold text-ink">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-ink">{{ $paginator->total() }}</span>
                </span>
            </div>
        </div>

        @if ($paginator->total() === 0)
            <x-catalog.empty-state
                :term="$currentSearch"
                :reset-url="route($baseRoute, $baseParams)"
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] border-collapse text-left">
                    <caption class="sr-only">Product list with ID, name, description, price, picture and customize actions</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="w-20 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ID</th>
                            <th scope="col" class="w-64 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">NAME</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">DESCRIPTION</th>
                            <th scope="col" class="w-48 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PRICE</th>
                            <th scope="col" class="w-28 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PICTURE</th>
                            <th scope="col" class="w-40 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">CUSTOMIZE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($paginator as $product)
                            <tr class="group transition-colors duration-150 hover:bg-line/40">
                                {{-- ID --}}
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-grid h-8 min-w-8 place-items-center rounded-lg bg-surface-strong px-2 text-[12px] font-bold tabular-nums text-ink ring-1 ring-line transition group-hover:bg-accent/12 group-hover:text-accent group-hover:ring-accent/25">
                                        {{ $product->displayId() }}
                                    </span>
                                </td>

                                {{-- NAME --}}
                                <td class="px-4 py-3 align-middle">
                                    <a href="{{ route('products.show', $product->id) }}" class="block group/name">
                                        <span class="block text-[13.5px] font-semibold text-ink transition group-hover/name:text-accent">{{ $product->name }}</span>
                                        <span class="mt-0.5 block text-[11px] text-muted">{{ $product->category->label() }}</span>
                                    </a>
                                </td>

                                {{-- DESCRIPTION --}}
                                <td class="px-4 py-3 align-middle">
                                    <p class="max-w-md text-[12.5px] leading-relaxed text-muted">{{ $product->description }}</p>
                                </td>

                                {{-- PRICE --}}
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-flex items-center rounded-lg bg-accent/[0.08] px-2.5 py-1.5 text-[12.5px] font-semibold tabular-nums text-accent ring-1 ring-inset ring-accent/20">
                                        {{ $product->priceLabel() }}
                                    </span>
                                </td>

                                {{-- PICTURE --}}
                                <td class="px-4 py-3 text-right align-middle">
                                    <div class="flex justify-end">
                                        <x-catalog.product-thumbnail :product="$product" size="sm" />
                                    </div>
                                </td>

                                {{-- CUSTOMIZE --}}
                                <td class="px-4 py-3 text-right align-middle">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('products.show', $product->id) }}" title="View {{ $product->name }}" aria-label="View {{ $product->name }}" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong text-muted transition hover:border-accent/40 hover:text-accent">
                                            <x-app.icon name="eye" />
                                        </a>
                                        @if ($editable)
                                            <a href="{{ route('products.edit', $product->id) }}" title="Edit {{ $product->name }}" aria-label="Edit {{ $product->name }}" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong text-muted transition hover:border-accent/40 hover:text-accent">
                                                <x-app.icon name="edit" />
                                            </a>
                                        @endif
                                        @if ($deletable)
                                            <x-app.confirm-form
                                                :action="route('products.destroy', $product->id)"
                                                method="DELETE"
                                                compact
                                                icon="trash"
                                                title="Delete this product?"
                                                :message="'<span class=&quot;font-semibold text-ink&quot;>'.e($product->name).'</span> will be moved to Deleted Items and can be restored later.'"
                                                confirm-label="Yes, delete"
                                            />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($paginator->hasPages())
                <x-catalog.pager :paginator="$paginator" />
            @endif
        @endif
    </div>
</div>
