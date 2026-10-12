<x-layouts.app :active="'category-'.$product->category->slug()" :page-title="$pageTitle">
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-muted">/</span>
        <a href="{{ route('products.index') }}" class="transition hover:text-accent">Catalog</a>
        <span class="text-muted">/</span>
        <a href="{{ route('products.show', $product->id) }}" class="transition hover:text-accent">{{ $product->name }}</a>
        <span class="text-muted">/</span>
        <span class="text-muted">Edit</span>
    </nav>

    <x-app.page-header
        :title="'Edit '.$product->name"
        eyebrow="Catalog"
        description="Update this catalog entry. The ID, Name, Description, Price and Picture columns are refreshed immediately."
    />

    <x-catalog.product-form
        :product="$product"
        :categories="$categories"
        :selected-category="$selectedCategory"
        :action="route('products.update', $product->id)"
        method="PATCH"
        submit-label="Save changes"
        :can-delete="$canDelete"
    />
</x-layouts.app>
