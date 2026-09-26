<x-layouts.app active="products.index" :page-title="$pageTitle">
    <x-app.page-header
        title="All Products"
        eyebrow="Catalog"
        :description="'Every item in the Tech Components catalog &mdash; '.$totalProducts.' products across 5 subtopics.'"
    >
        <x-slot:actions>
            <a href="{{ route('products.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(249,115,22,0.8)] transition hover:bg-accent-soft active:scale-[0.98]">
                <x-app.icon name="plus" />
                Add Product
            </a>
        </x-slot:actions>
    </x-app.page-header>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        <x-ui.stat-card label="Total Products" :value="$totalProducts" hint="in catalog" icon="layers" active />
        @foreach ($categories as $category)
            <x-ui.stat-card
                :label="$category->shortLabel()"
                :value="$counts[$category->value]"
                hint="products"
                :icon="$category->slug()"
                :href="route('categories.'.$category->slug())"
            />
        @endforeach
    </div>

    <x-catalog.product-table
        :paginator="$products"
        :filters="$filters"
        :categories="$categories"
    />
</x-layouts.app>
