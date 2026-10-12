<x-layouts.app active="inventory.index" :page-title="$pageTitle">
    <x-app.page-header
        :title="$pageTitle"
        eyebrow="Inventory"
        description="Browse, search, and manage the products tracked by inventory."
    />

    <x-catalog.product-table
        :paginator="$products"
        :categories="$categories"
        :filters="$filters"
        :editable="$canManage"
        :deletable="$canDelete"
        base-route="inventory.products"
    />
</x-layouts.app>
