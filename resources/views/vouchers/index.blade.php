<x-layouts.app active="vouchers.index" :page-title="$pageTitle">
    <x-app.page-header
        title="My Vouchers"
        eyebrow="Account"
        description="Available discount vouchers and promotional offers for your next hardware upgrade."
    />

    <div class="rounded-2xl border border-line bg-white p-6 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-accent/30 bg-accent/[0.04] p-5">
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-accent/15 px-2.5 py-1 text-[11px] font-bold text-accent">TECH COMPONENTS</span>
                    <span class="text-[12px] font-semibold text-muted">Valid till Nov 30, 2026</span>
                </div>
                <h3 class="mt-3 text-[16px] font-bold text-ink">10% Off Any Component</h3>
                <p class="mt-1 text-[12.5px] text-muted">Use code <code class="rounded bg-surface-strong px-1.5 py-0.5 font-mono font-semibold text-accent">UPGRADE10</code> at checkout.</p>
            </div>
            <div class="rounded-xl border border-line bg-surface-strong p-5 opacity-75">
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-surface px-2.5 py-1 text-[11px] font-bold text-muted">STOREWIDE</span>
                    <span class="text-[12px] font-semibold text-muted">Expired</span>
                </div>
                <h3 class="mt-3 text-[16px] font-bold text-ink">Free Express Shipping</h3>
                <p class="mt-1 text-[12.5px] text-muted">Code <code class="rounded bg-panel px-1.5 py-0.5 font-mono text-muted">FREESHIP</code></p>
            </div>
        </div>
    </div>
</x-layouts.app>
