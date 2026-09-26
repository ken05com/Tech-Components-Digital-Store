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
        title="Dashboard"
        eyebrow="Overview"
        description="Live overview of the Tech Components catalog &mdash; 25 products across the 5 subtopics defined in the source document."
    >
        <x-slot:actions>
            <a href="{{ route('products.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-white/[0.05] px-4 text-[13px] font-semibold text-slate-200 ring-1 ring-line transition hover:bg-white/10 hover:text-white">
                <x-app.icon name="layers" />
                View all products
            </a>
        </x-slot:actions>
    </x-app.page-header>

    {{-- Summary cards --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <x-ui.stat-card
            label="Total Products"
            :value="$totalProducts"
            hint="in catalog"
            icon="layers"
            :href="route('products.index')"
            active
        />

        @foreach ($categories as $category)
            <x-ui.stat-card
                :label="$category->label()"
                :value="$counts[$category->value]"
                hint="products"
                :icon="$category->slug()"
                :href="route('categories.'.$category->slug())"
            />
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid gap-4 xl:grid-cols-5">

        {{-- Category distribution --}}
        <div class="rounded-2xl border border-line bg-panel p-5 xl:col-span-2">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[14px] font-semibold text-slate-100">Category Distribution</h2>
                    <p class="mt-1 text-[11.5px] text-slate-500">Share of the {{ $totalProducts }} catalog items per subtopic.</p>
                </div>
                <x-ui.badge label="Live" tone="emerald" dot />
            </div>

            <div class="relative mx-auto mt-4 h-52 w-full max-w-[260px]">
                <canvas id="chartDistribution" aria-label="Product count by category" role="img"></canvas>
                <div class="pointer-events-none absolute inset-0 grid place-items-center">
                    <div class="text-center">
                        <p class="text-[26px] font-bold leading-none text-white">{{ $totalProducts }}</p>
                        <p class="mt-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Products</p>
                    </div>
                </div>
            </div>

            <ul class="mt-5 space-y-2">
                @foreach ($categoryStats as $stat)
                    <li>
                        <a href="{{ route('categories.'.$stat['slug']) }}" class="group flex items-center gap-2.5 rounded-xl px-2 py-1.5 transition hover:bg-white/[0.04]">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ \App\Catalog\ChartPalette::color($stat['slug']) }}"></span>
                            <span class="min-w-0 flex-1 truncate text-[12.5px] text-slate-300 transition group-hover:text-white">{{ $stat['category'] }}</span>
                            <span class="text-[12px] font-bold tabular-nums text-slate-200">{{ $stat['count'] }}</span>
                            <span class="w-11 text-right text-[11px] tabular-nums text-slate-500">{{ round($stat['count'] / max(1, $totalProducts) * 100) }}%</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Price overview --}}
        <div class="rounded-2xl border border-line bg-panel p-5 xl:col-span-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[14px] font-semibold text-slate-100">Price Overview by Category</h2>
                    <p class="mt-1 text-[11.5px] text-slate-500">Lowest and highest price listed for each subtopic, in pesos.</p>
                </div>
                <x-ui.badge label="PHP" tone="slate" />
            </div>

            <div class="mt-4 h-64 w-full">
                <canvas id="chartPrices" aria-label="Price range per category" role="img"></canvas>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-line bg-shell/50 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Lowest Listed</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-white">₱{{ number_format($overview['lowest']) }}</p>
                    <p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $overview['cheapest']->name }}</p>
                </div>
                <div class="rounded-xl border border-line bg-shell/50 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Highest Listed</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-accent">₱{{ number_format($overview['highest']) }}</p>
                    <p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $overview['dearest']->name }}</p>
                </div>
                <div class="rounded-xl border border-line bg-shell/50 p-3.5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Average Midpoint</p>
                    <p class="mt-1.5 text-[17px] font-bold tabular-nums text-white">₱{{ number_format($overview['average']) }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-500">across all {{ $totalProducts }} products</p>
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
                <h2 class="text-[13px] font-semibold text-slate-200">Recent Products</h2>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-slate-400 transition hover:text-accent">
                View all {{ $totalProducts }}
                <x-app.icon name="arrow-right" />
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left">
                <caption class="sr-only">Most recently added products</caption>
                <thead>
                    <tr class="border-b border-line bg-shell/40">
                        <th scope="col" class="w-20 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">ID</th>
                        <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">NAME</th>
                        <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">DESCRIPTION</th>
                        <th scope="col" class="w-48 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">PRICE</th>
                        <th scope="col" class="w-28 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">PICTURE</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/70">
                    @foreach ($recent as $product)
                        <tr class="group transition-colors duration-150 hover:bg-white/[0.028]">
                            <td class="px-4 py-3 align-middle">
                                <span class="inline-grid h-8 min-w-8 place-items-center rounded-lg bg-white/[0.05] px-2 text-[12px] font-bold tabular-nums text-slate-300 ring-1 ring-line transition group-hover:bg-accent/12 group-hover:text-accent group-hover:ring-accent/25">{{ $product->displayId() }}</span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <a href="{{ route('products.show', $product->id) }}" class="block">
                                    <span class="block text-[13.5px] font-semibold text-slate-100 transition group-hover:text-accent">{{ $product->name }}</span>
                                    <span class="mt-0.5 block text-[11px] text-slate-500">{{ $product->category->label() }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <p class="max-w-md text-[12.5px] leading-relaxed text-slate-400">{{ $product->description }}</p>
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
    </div>

    {{-- Category quick links --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($categories as $category)
            <a href="{{ route('categories.'.$category->slug()) }}" class="group relative overflow-hidden rounded-2xl border border-line bg-panel p-4 transition-all duration-200 hover:-translate-y-0.5 hover:border-accent/30 hover:bg-panel-raised">
                <span class="grid h-10 w-10 place-items-center rounded-xl text-[12px] font-bold transition-colors duration-200"
                      style="background: {{ \App\Catalog\ChartPalette::color($category->slug()) }}1f; color: {{ \App\Catalog\ChartPalette::color($category->slug()) }}; box-shadow: inset 0 0 0 1px {{ \App\Catalog\ChartPalette::color($category->slug()) }}33;">
                    {{ $category->monogram() }}
                </span>
                <p class="mt-3 truncate text-[12.5px] font-semibold text-slate-100 transition group-hover:text-accent">{{ $category->label() }}</p>
                <p class="mt-0.5 text-[11px] text-slate-500">{{ $counts[$category->value] }} products &middot; subtopic {{ $loop->iteration }}</p>
            </a>
        @endforeach
    </div>
</x-layouts.app>
