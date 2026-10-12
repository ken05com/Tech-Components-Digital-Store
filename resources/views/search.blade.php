<x-layouts.app active="search" :page-title="$pageTitle">
    <x-app.page-header
        title="Search"
        eyebrow="Utilities"
        :description="'Found '.$matchCount.' matching '.($matchCount === 1 ? 'product' : 'products').' for &ldquo;'.$term.'&rdquo; across the whole catalog.'"
    >
        <x-slot:actions>
            <a href="{{ route('products.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-surface-strong px-4 text-[13px] font-semibold text-ink ring-1 ring-line transition hover:bg-line/40 hover:text-ink">
                <x-app.icon name="layers" />
                Browse all
            </a>
        </x-slot:actions>
    </x-app.page-header>

    <div class="rounded-2xl border border-line bg-panel p-4">
        <form method="GET" action="{{ route('search') }}" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                    <x-app.icon name="search" />
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $term }}"
                    autofocus
                    placeholder="Search products, descriptions or categories…"
                    aria-label="Search the catalog"
                    class="h-11 w-full rounded-xl border border-line bg-surface-strong pl-10 pr-3 text-[13px] text-ink placeholder:text-muted transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20"
                >
            </div>
            <div class="relative">
                <select name="category" onchange="this.form.submit()" aria-label="Filter by category" class="h-11 w-full appearance-none rounded-xl border border-line bg-surface-strong pl-3 pr-8 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20 sm:w-52">
                    <option value="all" @selected($filters['category'] === 'all')>All categories</option>
                    @foreach ($categories as $option)
                        <option value="{{ $option->value }}" @selected($filters['category'] === $option->value)>{{ $option->label() }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 rotate-90 text-muted"><x-app.icon name="chevron-right" /></span>
            </div>
            <button type="submit" class="h-11 shrink-0 rounded-xl bg-accent px-5 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(201,0,22,0.8)] transition hover:bg-accent-soft">
                Search
            </button>
        </form>
    </div>

    <x-catalog.product-table
        :paginator="$products"
        :filters="$filters"
        :categories="$categories"
        base-route="search"
        search-param="q"
        :editable="$canManage"
        :deletable="$canDelete"
    />
</x-layouts.app>
