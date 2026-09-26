<x-layouts.app active="settings" :page-title="$pageTitle">
    <x-app.page-header
        title="Settings"
        eyebrow="Utilities"
        description="Catalog configuration. This project ships without a database &mdash; the 25 products are read straight from the source document."
    />

    <div class="grid gap-4 lg:grid-cols-3">

        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                        <x-app.icon name="layers" />
                    </span>
                    <div>
                        <h2 class="text-[14px] font-semibold text-slate-100">Catalog structure</h2>
                        <p class="text-[11.5px] text-slate-500">The five subtopics are fixed and cannot be edited.</p>
                    </div>
                </div>

                <ul class="mt-4 space-y-2">
                    @foreach ($categories as $index => $category)
                        <li class="flex items-center gap-3 rounded-xl border border-line bg-shell/50 px-3.5 py-3 transition hover:border-accent/25">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl text-[11px] font-bold"
                                  style="background: {{ \App\Catalog\ChartPalette::withAlpha($category->slug(), 0.14) }}; color: {{ \App\Catalog\ChartPalette::color($category->slug()) }};">
                                {{ $category->monogram() }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold text-slate-100">{{ $category->label() }}</span>
                                <span class="block truncate text-[11px] text-slate-500">/categories/{{ $category->slug() }}</span>
                            </span>
                            <span class="shrink-0 rounded-lg bg-white/[0.05] px-2 py-1 text-[11px] font-semibold tabular-nums text-slate-300 ring-1 ring-line">
                                {{ $counts[$category->value] }} items
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[14px] font-semibold text-slate-100">Appearance</h2>
                <p class="mt-1 text-[11.5px] text-slate-500">The dashboard uses a fixed dark theme with an orange accent.</p>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @foreach (['#f97316' => 'Accent', '#0a0b0e' => 'Shell', '#14171d' => 'Panel', '#23272f' => 'Line', '#e8eaed' => 'Text'] as $hex => $label)
                        <span class="inline-flex items-center gap-2 rounded-xl border border-line bg-shell/50 px-2.5 py-1.5">
                            <span class="h-4 w-4 rounded-md ring-1 ring-white/10" style="background: {{ $hex }}"></span>
                            <span class="text-[11px] font-medium text-slate-400">{{ $label }}</span>
                            <span class="font-mono text-[10.5px] uppercase text-slate-600">{{ $hex }}</span>
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[14px] font-semibold text-slate-100">Data source</h2>
                <dl class="mt-3 space-y-2.5 text-[12.5px]">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500">Storage</dt>
                        <dd class="font-semibold text-slate-200">Static catalog (no database)</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500">Products</dt>
                        <dd class="font-semibold tabular-nums text-slate-200">{{ $totalProducts }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500">Subtopics</dt>
                        <dd class="font-semibold tabular-nums text-slate-200">5</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-500">Local edits</dt>
                        <dd class="font-semibold text-slate-200">{{ $hasLocalChanges ? 'Applied' : 'None' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-line bg-panel p-5">
                <h2 class="text-[14px] font-semibold text-slate-100">Restore document data</h2>
                <p class="mt-1.5 text-[11.5px] leading-relaxed text-slate-500">
                    Any product you add, edit or delete is stored in a local overlay file. Resetting discards those changes and brings back the original 25 products exactly as written in the document.
                </p>

                <form method="POST" action="{{ route('catalog.reset') }}" class="mt-4"
                      data-confirm="Restore the original 25 products? Your local changes will be discarded.">
                    @csrf
                    <button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-white/[0.05] px-4 text-[13px] font-semibold text-slate-200 ring-1 ring-line transition hover:bg-white/10 hover:text-white">
                        <x-app.icon name="refresh" />
                        Restore document data
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
