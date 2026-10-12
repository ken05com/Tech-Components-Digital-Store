@php
    $isEdit = $category !== null;
    $action = $isEdit ? route('categories.update', $category->id) : route('categories.store');
@endphp

<x-layouts.app active="categories.index" :page-title="$pageTitle">
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-muted">/</span>
        <a href="{{ route('categories.index') }}" class="transition hover:text-accent">Collections</a>
        <span class="text-muted">/</span>
        <span class="text-muted">{{ $isEdit ? 'Edit' : 'Add New' }}</span>
    </nav>

    <x-app.page-header
        :title="$isEdit ? 'Edit '.$category->label() : 'Add New Collection'"
        eyebrow="Management"
        description="A collection becomes a navigation link in the sidebar and a subtopic on the storefront."
    />

    <form method="POST" action="{{ $action }}" class="w-full space-y-5">
        @csrf
        @if ($isEdit) @method('PATCH') @endif

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="layers" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Collection Details</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $category?->label()) }}"
                        required
                        maxlength="120"
                        placeholder="e.g. Gaming Peripherals"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('name'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="short_label" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">
                        Short label <span class="normal-case tracking-normal text-muted">(optional)</span>
                    </label>
                    <input
                        id="short_label"
                        name="short_label"
                        type="text"
                        value="{{ old('short_label', $category?->shortLabel()) }}"
                        maxlength="40"
                        placeholder="e.g. Peripherals"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('short_label'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('short_label'),
                        ])
                    >
                    @error('short_label') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>



                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">
                        Description <span class="normal-case tracking-normal text-muted">(optional)</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="255"
                        placeholder="Short summary shown on the collection page."
                        @class([
                            'w-full resize-y rounded-xl border bg-surface-strong px-3.5 py-3 text-[13px] leading-relaxed text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('description'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('description'),
                        ])
                    >{{ old('description', $category?->description()) }}</textarea>
                    @error('description') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @if ($isEdit)
                <p class="mt-4 rounded-xl bg-surface-strong px-3.5 py-2.5 text-[11.5px] leading-relaxed text-muted ring-1 ring-line">
                    This collection currently holds
                    <span class="font-semibold text-ink">{{ $productCount ?? 0 }}</span>
                    {{ ($productCount ?? 0) === 1 ? 'product' : 'products' }}.
                </p>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('categories.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl px-4 text-[13px] font-semibold text-muted transition hover:bg-line/40 hover:text-ink">
                Cancel
            </a>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(201,0,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                <x-app.icon name="plus" />
                {{ $isEdit ? 'Save changes' : 'Add New Collection' }}
            </button>
        </div>
    </form>
</x-layouts.app>
