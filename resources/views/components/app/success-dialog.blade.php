@if (session('success_dialog'))
    <div
        x-data="{ open: true }"
        x-cloak
        x-show="open"
        class="fixed inset-0 z-[70] grid place-items-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="success-dialog-title"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            @click="open = false"
            class="absolute inset-0 bg-black/75 backdrop-blur-sm"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            @click.stop
            @keydown.escape.window="open = false"
            class="relative w-full max-w-sm overflow-hidden rounded-2xl border border-line bg-panel text-center shadow-2xl"
        >
            <div class="p-6">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-500/12 text-emerald-600 ring-1 ring-emerald-500/25">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-7 w-7" aria-hidden="true">
                        <path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <h2 id="success-dialog-title" class="mt-4 text-[15px] font-semibold text-ink">{{ session('success_dialog') }}</h2>
                <p class="mt-1.5 text-[12.5px] text-muted">The catalog has been updated.</p>
            </div>
            <div class="border-t border-line bg-line/30 px-6 py-3.5">
                <button
                    type="button"
                    @click="open = false"
                    autofocus
                    class="h-10 w-full rounded-xl bg-accent px-4 text-[13px] font-semibold text-white transition hover:bg-accent-soft active:scale-[0.99]"
                >
                    OK
                </button>
            </div>
        </div>
    </div>
@endif
