<x-layouts.app active="settings" :page-title="$pageTitle">
    <x-app.page-header
        title="Settings"
        eyebrow="Utilities"
        description="Manage the author information and reflection answers shown on the dashboard, and review the fixed catalog structure."
    />

    <div class="grid gap-4 lg:grid-cols-3">

        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                        <x-app.icon name="layers" />
                    </span>
                    <div>
                        <h2 class="text-[14px] font-semibold text-ink">Catalog structure</h2>
                        <p class="text-[11.5px] text-muted">The five subtopics are fixed and cannot be edited.</p>
                    </div>
                </div>

                <ul class="mt-4 space-y-2">
                    @foreach ($categories as $index => $category)
                        <li class="flex items-center gap-3 rounded-xl border border-line bg-surface-strong px-3.5 py-3 transition hover:border-accent/25">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl"
                                  style="background: {{ \App\Catalog\ChartPalette::withAlpha($category->slug(), 0.14) }}; color: {{ \App\Catalog\ChartPalette::color($category->slug()) }};">
                                <x-app.icon :name="$category->slug()" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-ink">{{ $category->label() }}</span>
                                <span class="block truncate text-[11px] text-muted">/categories/{{ $category->slug() }}</span>
                            </span>
                            <span class="shrink-0 rounded-lg bg-surface-strong px-2 py-1 text-[11px] font-semibold tabular-nums text-ink ring-1 ring-line">
                                {{ $counts[$category->value] }} items
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            @can('manage-settings')
                <div class="rounded-2xl border border-line bg-panel p-5">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                            <x-app.icon name="user" />
                        </span>
                        <div>
                            <h2 class="text-[14px] font-semibold text-ink">Author &amp; reflections</h2>
                            <p class="text-[11.5px] text-muted">Shown on the dashboard. Saved to the settings table.</p>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-rose-500/25 bg-rose-500/[0.07] px-4 py-3 text-[12px] text-rose-700">
                            Please correct the highlighted fields and try again.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.update') }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="author_name" class="block text-[11.5px] font-semibold text-ink">Author name</label>
                                <input id="author_name" name="author_name" type="text" required maxlength="120" value="{{ old('author_name', $author['name']) }}"
                                    class="mt-1.5 h-10 w-full rounded-xl border border-line bg-surface-strong px-3 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20">
                                @error('author_name') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="author_address" class="block text-[11.5px] font-semibold text-ink">Address</label>
                                <input id="author_address" name="author_address" type="text" maxlength="255" value="{{ old('author_address', $author['address']) }}"
                                    class="mt-1.5 h-10 w-full rounded-xl border border-line bg-surface-strong px-3 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20">
                                @error('author_address') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="author_image" class="block text-[11.5px] font-semibold text-ink">Profile image path or URL</label>
                            <input id="author_image" name="author_image" type="text" maxlength="255" value="{{ old('author_image', $author['image']) }}"
                                class="mt-1.5 h-10 w-full rounded-xl border border-line bg-surface-strong px-3 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20">
                            <p class="mt-1 text-[11px] text-muted">Use a path such as <code class="text-muted">images/profile-avatar.jpg</code> or a full URL.</p>
                            @error('author_image') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        @foreach ($reflections as $reflection)
                            <div class="rounded-xl border border-line bg-line/30 p-3.5">
                                <label for="{{ $reflection['key'] }}_title" class="block text-[11.5px] font-semibold text-ink">Reflection {{ $loop->iteration }} title</label>
                                <input id="{{ $reflection['key'] }}_title" name="{{ $reflection['key'] }}_title" type="text" required maxlength="160"
                                    value="{{ old($reflection['key'].'_title', $reflection['title']) }}"
                                    class="mt-1.5 h-10 w-full rounded-xl border border-line bg-surface-strong px-3 text-[13px] text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20">
                                <label for="{{ $reflection['key'] }}_body" class="mt-3 block text-[11.5px] font-semibold text-ink">Reflection {{ $loop->iteration }} answer</label>
                                <textarea id="{{ $reflection['key'] }}_body" name="{{ $reflection['key'] }}_body" required maxlength="1000" rows="3"
                                    class="mt-1.5 w-full rounded-xl border border-line bg-surface-strong px-3 py-2.5 text-[13px] leading-relaxed text-ink transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20">{{ old($reflection['key'].'_body', $reflection['body']) }}</textarea>
                                @error($reflection['key'].'_title') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                                @error($reflection['key'].'_body') <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        @endforeach

                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(201,0,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                            <x-app.icon name="edit" />
                            Save settings
                        </button>
                    </form>
                </div>
            @endcan
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[14px] font-semibold text-ink">Data source</h2>
                <dl class="mt-3 space-y-2.5 text-[12.5px]">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Storage</dt>
                        <dd class="font-semibold text-ink">MySQL database</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Products</dt>
                        <dd class="font-semibold tabular-nums text-ink">{{ $totalProducts }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Subtopics</dt>
                        <dd class="font-semibold tabular-nums text-ink">5</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Deleted items</dt>
                        <dd class="font-semibold tabular-nums text-ink">{{ $deletedCount }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted">Local edits</dt>
                        <dd class="font-semibold text-ink">{{ $hasLocalChanges ? 'Applied' : 'None' }}</dd>
                    </div>
                </dl>
            </div>

            @can('reset-catalog')
                <div class="rounded-2xl border border-line bg-panel p-5">
                    <h2 class="text-[14px] font-semibold text-ink">Restore document data</h2>
                    <p class="mt-1.5 text-[11.5px] leading-relaxed text-muted">
                        Adds, edits and deletes are stored in the database. Resetting wipes the products table and brings back the original 25 products exactly as written in the source document.
                    </p>
                    <div class="mt-4">
                        <x-app.confirm-form
                            :action="route('catalog.reset')"
                            method="POST"
                            icon="refresh"
                            tone="accent"
                            title="Restore the original data?"
                            message="The products table will be wiped and the original 25 products restored. This cannot be undone."
                            confirm-label="Yes, restore"
                            trigger-label="Restore document data"
                        />
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>
