@push('scripts')
    <script>
        window.__techCharts = {
            distribution: {
                labels: @json(collect($categoryStats)->pluck('category')),
                counts: @json(collect($categoryStats)->pluck('count')),
                colors: @json(collect($categoryStats)->map(fn ($s) => \App\Catalog\ChartPalette::color($s['slug']))),
            },
            prices: {
                labels: @json(collect($categoryStats)->pluck('category')),
                min: @json(collect($categoryStats)->pluck('min')),
                max: @json(collect($categoryStats)->pluck('max')),
            },
        };
    </script>
@endpush

@php
    use App\Catalog\Category;

    $accentTotal = $totalProducts;
@endphp

<x-layouts.app active="dashboard" :page-title="$pageTitle">
    <x-app.page-header
        title="Tech Components Dashboard"
        eyebrow="Dashboard"
        description="Digital store and inventory catalog for computer hardware &mdash; live figures drawn straight from the database across the 5 subtopics in the source document."
    />

    {{-- Charts --}}
    <div class="grid gap-4 xl:grid-cols-5">

        {{-- Category distribution --}}
        <div class="rounded-2xl border border-line bg-panel p-5 xl:col-span-2">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[14px] font-semibold text-ink">Category Distribution</h2>
                    <p class="mt-1 text-[11.5px] text-muted">Share of the {{ $totalProducts }} catalog items per subtopic.</p>
                </div>
                <x-ui.badge label="Live" tone="emerald" dot />
            </div>

            <div class="relative mx-auto mt-4 h-52 w-full max-w-[260px]">
                <canvas id="chartDistribution" aria-label="Product count by category" role="img"></canvas>
                <div class="pointer-events-none absolute inset-0 grid place-items-center">
                    <div class="text-center">
                        <p class="text-[26px] font-bold leading-none text-ink">{{ $totalProducts }}</p>
                        <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-muted">Products</p>
                    </div>
                </div>
            </div>

            <ul class="mt-5 space-y-2">
                @foreach ($categoryStats as $stat)
                    <li>
                        <a href="{{ route('categories.overview', $stat['slug']) }}" class="group flex items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-line/40">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ \App\Catalog\ChartPalette::color($stat['slug']) }}"></span>
                            <span class="min-w-0 flex-1 truncate text-[12.5px] text-ink transition group-hover:text-ink">{{ $stat['category'] }}</span>
                            <span class="text-[12px] font-bold tabular-nums text-ink">{{ $stat['count'] }}</span>
                            <span class="w-11 text-right text-[11px] tabular-nums text-muted">{{ round($stat['count'] / max(1, $totalProducts) * 100) }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Price overview --}}
        <div class="rounded-2xl border border-line bg-panel p-5 xl:col-span-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[14px] font-semibold text-ink">Price Overview by Category</h2>
                    <p class="mt-1 text-[11.5px] text-muted">Lowest and highest price listed for each subtopic, in pesos.</p>
                </div>
                <x-ui.badge label="PHP" tone="slate" />
            </div>

            <div class="mt-4 h-64 w-full">
                <canvas id="chartPrices" aria-label="Price range per category" role="img"></canvas>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-line bg-surface-strong p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Lowest Listed</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-ink">₱{{ number_format($overview['lowest']) }}</p>
                    <p class="mt-0.5 truncate text-[11px] text-muted">{{ $overview['cheapest']->name }}</p>
                </div>
                <div class="rounded-xl border border-line bg-surface-strong p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Highest Listed</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-accent">₱{{ number_format($overview['highest']) }}</p>
                    <p class="mt-0.5 truncate text-[11px] text-muted">{{ $overview['dearest']->name }}</p>
                </div>
                <div class="rounded-xl border border-line bg-surface-strong p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-muted">Average Midpoint</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-ink">₱{{ number_format($overview['average']) }}</p>
                    <p class="mt-0.5 text-[11px] text-muted">across all {{ $totalProducts }} products</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent products --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-panel">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Recent Products</h2>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left">
                <caption class="sr-only">Paginated products catalog</caption>
                <thead>
                    <tr class="border-b border-line bg-line/30">
                        <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">NAME</th>
                        <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">DESCRIPTION</th>
                        <th scope="col" class="w-48 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PRICE</th>
                        <th scope="col" class="w-28 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PICTURE</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/70">
                    @foreach ($products as $product)
                        <tr class="group transition-colors duration-150 hover:bg-line/40">
                            <td class="px-4 py-3 align-middle">
                                <a href="{{ route('products.show', $product->id) }}" class="block">
                                    <span class="block text-[13.5px] font-semibold text-ink transition group-hover:text-accent">{{ $product->name }}</span>
                                    <span class="mt-0.5 block text-[11px] text-muted">{{ $product->category->label() }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <p class="max-w-md text-[12.5px] leading-relaxed text-muted">{{ $product->description }}</p>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-flex items-center rounded-lg bg-accent/[0.08] px-2.5 py-1.5 text-[12.5px] font-semibold tabular-nums text-accent ring-1 ring-inset ring-accent/20">{{ $product->priceLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right align-middle">
                                <x-catalog.product-thumbnail :product="$product" size="sm" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-catalog.pager :paginator="$products" />
    </div>

    {{-- Category quick links --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($categories as $category)
            <a href="{{ route('categories.overview', $category->slug()) }}" class="group relative overflow-hidden rounded-2xl border border-line bg-panel p-4 transition-all duration-200 hover:-translate-y-0.5 hover:border-accent/30 hover:bg-panel-raised">
                <p class="truncate text-[12.5px] font-semibold text-ink transition group-hover:text-accent">{{ $category->label() }}</p>
                <p class="mt-0.5 text-[11px] text-muted">{{ $counts[$category->value] }} products &middot; subtopic {{ $loop->iteration }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>
