@props([
    'product',
    'open' => false,
])

<div x-data="{ open: @js($open) }" x-cloak>
    <button
        type="button"
        @click="open = true"
        class="inline-flex h-9 items-center gap-2 rounded-xl border border-rose-500/25 bg-rose-500/[0.07] px-3.5 text-[12.5px] font-semibold text-rose-300 transition hover:border-rose-500/45 hover:bg-rose-500/[0.12]"
    >
        <x-app.icon name="trash" />
        Delete
    </button>

    <div
        x-show="open"
        class="fixed inset-0 z-[60] grid place-items-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-title-{{ $product->id }}"
    >
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
            class="absolute inset-0 bg-black/75 backdrop-blur-sm"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
            @keydown.escape.window="open = false"
            class="relative w-full max-w-md overflow-hidden rounded-2xl border border-line bg-panel shadow-2xl"
        >
            <div class="flex gap-3.5 p-5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-rose-500/12 text-rose-400 ring-1 ring-rose-500/25">
                    <x-app.icon name="trash" />
                </span>
                <div>
                    <h3 id="delete-title-{{ $product->id }}" class="text-[15px] font-semibold text-white">Delete this product?</h3>
                    <p class="mt-1.5 text-[12.5px] leading-relaxed text-slate-400">
                        <span class="font-semibold text-slate-200">{{ $product->name }}</span> will be removed from
                        <span class="text-slate-300">{{ $product->category->label() }}</span>. This only affects your local catalog copy &mdash; use
                        <em>Restore document data</em> in Settings to bring the original 25 products back.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line bg-shell/40 px-5 py-3.5">
                <button type="button" @click="open = false" class="h-9 rounded-xl px-3.5 text-[12.5px] font-semibold text-slate-400 transition hover:bg-white/5 hover:text-slate-200">
                    Cancel
                </button>
                <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-9 rounded-xl bg-rose-500 px-4 text-[12.5px] font-semibold text-white transition hover:bg-rose-400 active:scale-[0.98]">
                        Yes, delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
