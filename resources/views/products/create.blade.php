<x-layouts.app active="products.create" :page-title="$pageTitle">
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-slate-700">/</span>
        <a href="{{ route('products.index') }}" class="transition hover:text-accent">Catalog</a>
        <span class="text-slate-700">/</span>
        <span class="text-slate-400">Add Product</span>
    </nav>

    <x-app.page-header
        title="Add Product"
        eyebrow="Catalog"
        description="Create a new catalog entry. Fields marked with the document's own column names keep every table consistent."
    />

    <x-catalog.product-form
        :product="null"
        :categories="$categories"
        :selected-category="$selectedCategory"
        :action="route('products.store')"
        method="POST"
        submit-label="Add Product"
    />
</x-layouts.app>
