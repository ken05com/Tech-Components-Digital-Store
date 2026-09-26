@php
    $subtopicNumber = array_search($category, \App\Catalog\Category::ordered(), true) + 1;
@endphp

<x-layouts.app :active="'category-'.$category->slug()" :page-title="$pageTitle">
    <x-app.page-header
        :title="$category->label()"
        :eyebrow="$category->label()"
        :description="$category->description()"
        badge
        badge-tone="accent"
    >
        <x-slot:actions>
            <a href="{{ route('products.create', ['category' => $category->value]) }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(249,115,22,0.8)] transition hover:bg-accent-soft active:scale-[0.98]">
                <x-app.icon name="plus" />
                Add Product
            </a>
        </x-slot:actions>
    </x-app.page-header>

    {{-- Summary cards --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Products" :value="$summary['products']" hint="in this subtopic" icon="layers" active />
        <x-ui.stat-card label="Product Category" :value="'Subtopic '.$subtopicNumber" hint="of 5" icon="tag" />
        <x-ui.stat-card label="Starting Price" :value="'₱'.number_format($summary['entry'])" hint="lowest listed" icon="layers" />
        <x-ui.stat-card label="Maximum Price" :value="'₱'.number_format($summary['exit'])" hint="highest listed" icon="layers" />
    </div>

    {{-- Subtopic position --}}
    <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-line bg-panel px-4 py-3">
        <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Catalog structure</span>
        <div class="flex flex-wrap items-center gap-1.5">
            @foreach (\App\Catalog\Category::ordered() as $sibling)
                <a
                    href="{{ route('categories.'.$sibling->slug()) }}"
                    @class([
                        'rounded-lg px-2.5 py-1.5 text-[11.5px] font-semibold transition',
                        'bg-accent/12 text-accent ring-1 ring-inset ring-accent/25' => $sibling === $category,
                        'bg-white/[0.04] text-slate-400 ring-1 ring-line hover:bg-white/[0.08] hover:text-slate-100' => $sibling !== $category,
                    ])
                >{{ $sibling->shortLabel() }}</a>
            @endforeach
        </div>
        <span class="ml-auto text-[11px] text-slate-500">{{ $summary['products'] }} of {{ $totalProducts }} catalog products</span>
    </div>

    {{-- Table --}}
    <x-catalog.product-table
        :paginator="$products"
        :filters="$filters"
        :locked-category="$category->value"
        base-route="categories.{{ $category->slug() }}"
    />
</x-layouts.app>
