<x-layouts.app active="categories.index" :page-title="$pageTitle">
    @php
        $emptyCount = $categories->filter(fn ($c) => ($counts[$c->id] ?? 0) === 0)->count();
    @endphp

    <x-app.page-header
        title="Collections"
        eyebrow="Management"
        :description="'The subtopics that shape the sidebar navigation and the storefront. '.$categories->count().' collections holding '.$totalProducts.' products.'"
    >
        <x-slot:actions>
            <a href="{{ route('categories.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(201,0,22,0.8)] transition hover:bg-accent-soft active:scale-[0.98]">
                <x-app.icon name="plus" />
                Add New Collection
            </a>
        </x-slot:actions>
    </x-app.page-header>

    {{-- Summary cards --}}
    <div class="grid gap-3 sm:grid-cols-3">
        <x-ui.stat-card label="Collections" :value="$categories->count()" hint="sidebar links" icon="layers" active />
        <x-ui.stat-card label="Total Products" :value="$totalProducts" hint="across collections" icon="tag" :href="route('products.index')" />
        <x-ui.stat-card label="Empty" :value="$emptyCount" hint="no products yet" icon="layers" />
    </div>

    {{-- Collections table --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-panel shadow-[0_1px_0_0_rgba(255,255,255,0.03)_inset]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Collection Overview</h2>
            </div>
            <span class="rounded-lg bg-surface-strong px-2 py-1 text-[11px] text-muted ring-1 ring-line">
                <span class="font-semibold text-ink">{{ $categories->count() }}</span> collections
            </span>
        </div>

        @if ($categories->isEmpty())
            <div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
                <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-surface-strong text-muted">
                    <x-app.icon name="layers" />
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-ink">No collections yet</p>
                    <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-muted">
                        Add your first collection to create a navigation link in the sidebar.
                    </p>
                </div>
                <a href="{{ route('categories.create') }}" class="mt-1 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white transition hover:bg-accent-soft">
                    <x-app.icon name="plus" />
                    Add New Collection
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] border-collapse text-left">
                    <caption class="sr-only">Collections with their product counts and customize actions</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="w-20 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ID</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">COLLECTION</th>
                            <th scope="col" class="w-44 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">SHORT LABEL</th>
                            <th scope="col" class="w-36 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">PRODUCTS</th>
                            <th scope="col" class="w-40 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">CUSTOMIZE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($categories as $record)
                            @php $count = $counts[$record->id] ?? 0; @endphp
                            <tr class="group transition-colors duration-150 hover:bg-line/40">
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-grid h-8 min-w-8 place-items-center rounded-lg bg-surface-strong px-2 text-[12px] font-bold tabular-nums text-ink ring-1 ring-line transition group-hover:bg-accent/12 group-hover:text-accent group-hover:ring-accent/25">
                                        {{ $record->id }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <a href="{{ route('categories.overview', $record->slug()) }}" class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-accent/12 text-accent ring-1 ring-accent/20">
                                            <x-app.icon :name="$record->slug()" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-[13.5px] font-semibold text-ink transition group-hover:text-accent">{{ $record->label() }}</span>
                                            <span class="mt-0.5 block truncate text-[11px] text-muted">/{{ $record->slug() }}</span>
                                        </span>
                                    </a>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="text-[12.5px] text-muted">{{ $record->shortLabel() }}</span>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-flex items-center rounded-lg px-2.5 py-1.5 text-[12.5px] font-semibold tabular-nums ring-1 ring-inset {{ $count > 0 ? 'bg-accent/[0.08] text-accent ring-accent/20' : 'bg-surface-strong text-muted ring-line' }}">
                                        {{ $count }} {{ $count === 1 ? 'item' : 'items' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right align-middle">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('categories.overview', $record->slug()) }}" title="Open {{ $record->label() }}" aria-label="Open {{ $record->label() }}" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong text-muted transition hover:border-accent/40 hover:text-accent">
                                            <x-app.icon name="eye" />
                                        </a>
                                        <a href="{{ route('categories.edit', $record->id) }}" title="Edit {{ $record->label() }}" aria-label="Edit {{ $record->label() }}" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong text-muted transition hover:border-accent/40 hover:text-accent">
                                            <x-app.icon name="edit" />
                                        </a>
                                        <x-app.confirm-form
                                            :action="route('categories.destroy', $record->id)"
                                            method="DELETE"
                                            compact
                                            icon="trash"
                                            title="Delete this collection?"
                                            :message="'<span class=&quot;font-semibold text-ink&quot;>'.e($record->label()).'</span> will be removed from the sidebar. Collections that still hold products cannot be deleted.'"
                                            confirm-label="Yes, delete"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
