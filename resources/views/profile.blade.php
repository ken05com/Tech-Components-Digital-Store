<x-layouts.app active="profile" :page-title="$pageTitle">
    <x-app.page-header
        title="Profile"
        eyebrow="Utilities"
        description="Catalog owner and project details."
    />

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-line bg-panel p-5 lg:col-span-2">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                <span class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-accent/20 ring-1 ring-accent/25">
                    <img
                        src="{{ asset('images/profile-avatar.jpg') }}"
                        alt="Profile photo of Shoby Keneth R. Daamo"
                        class="h-full w-full object-cover"
                        onerror="this.src='{{ asset('images/products/placeholder.svg') }}'"
                    >
                </span>
                <div class="min-w-0">
                    <h2 class="text-[19px] font-bold tracking-tight text-white">Shoby Keneth R. Daamo</h2>
                    <p class="mt-0.5 text-[13px] text-slate-400">CEO OWNER</p>
                    <div class="mt-2.5 flex flex-wrap gap-2">
                        <x-ui.badge label="Catalog owner" tone="accent" dot />
                        <x-ui.badge label="Tech Components" tone="slate" />
                    </div>
                </div>
            </div>

            <dl class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-line bg-shell/50 p-4">
                    <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Products</dt>
                    <dd class="mt-1.5 text-[20px] font-bold tabular-nums text-white">{{ $totalProducts }}</dd>
                </div>
                <div class="rounded-xl border border-line bg-shell/50 p-4">
                    <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Subtopics</dt>
                    <dd class="mt-1.5 text-[20px] font-bold tabular-nums text-white">{{ $categoryCount }}</dd>
                </div>
                <div class="rounded-xl border border-line bg-shell/50 p-4">
                    <dt class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Data source</dt>
                    <dd class="mt-1.5 text-[15px] font-bold text-accent">THE VIBE CODER</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-2xl border border-line bg-panel p-5">
            <h2 class="text-[14px] font-semibold text-slate-100">Project</h2>
            <p class="mt-2 text-[12.5px] leading-relaxed text-slate-400">
                TECH COMPONENTS is a dark themed Laravel dashboard and inventory catalog built around the subtopics and sample
                products defined in
            </p>
            <ul class="mt-4 space-y-2 text-[12.5px] text-slate-400">
                <li class="flex gap-2"><span class="text-accent">&bull;</span><span>Laravel &middot; Blade &middot; Alpine.js</span></li>
                <li class="flex gap-2"><span class="text-accent">&bull;</span><span>Tailwind CSS v4 with a custom dark palette</span></li>
                <li class="flex gap-2"><span class="text-accent">&bull;</span><span>Chart.js dashboard visualisations</span></li>
                <li class="flex gap-2"><span class="text-accent">&bull;</span><span>Static catalog &mdash; no database required</span></li>
            </ul>
        </div>
    </div>
</x-layouts.app>
