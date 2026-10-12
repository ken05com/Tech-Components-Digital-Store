<x-layouts.app active="deleted-items.index" :page-title="$pageTitle">
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-muted">/</span>
        <a href="{{ route('deleted-items.index') }}" class="transition hover:text-accent">Deleted Items</a>
        <span class="text-muted">/</span>
        <span class="text-muted">Restore</span>
    </nav>

    <x-app.page-header
        :title="'Restore '.$product->name"
        eyebrow="Deleted Items"
        description="Review and adjust this record, then restore it to the active catalog. The original picture is kept unless you upload a new one."
    >
        <x-slot:actions>
            <a href="{{ route('deleted-items.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40 hover:text-ink">
                <x-app.icon name="chevron-left" />
                Back to Deleted Items
            </a>
        </x-slot:actions>
    </x-app.page-header>

    <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/[0.05] px-4 py-3">
        <x-ui.badge label="Recoverable" tone="emerald" dot />
        <span class="text-[12.5px] text-ink">This product was removed from <span class="font-semibold text-ink">{{ $product->category->label() }}</span> and can be returned to the catalog.</span>
        @if ($product->deletedAt)
            <span class="ml-auto text-[11px] text-muted">Deleted {{ \Illuminate\Support\Carbon::parse($product->deletedAt)->diffForHumans() }}</span>
        @endif
    </div>

    <x-catalog.product-form
        :product="$product"
        :categories="$categories"
        :selected-category="$selectedCategory"
        :action="route('deleted-items.restore', $product->id)"
        method="PATCH"
        submit-label="Restore"
        :cancel-url="route('deleted-items.index')"
    />
</x-layouts.app>
