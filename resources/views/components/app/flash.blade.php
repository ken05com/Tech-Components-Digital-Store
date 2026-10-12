@if (session('status'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        role="status"
        class="flex items-start gap-3 rounded-2xl border border-emerald-500/25 bg-emerald-500/[0.07] px-4 py-3"
    >
        <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-emerald-500/15 text-emerald-600">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="h-3.5 w-3.5"><path d="m5 13 4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="flex-1 text-[13px] leading-relaxed text-emerald-700">{{ session('status') }}</p>
        <button type="button" @click="show = false" class="rounded-md p-1 text-emerald-600/60 transition hover:bg-emerald-500/10 hover:text-emerald-700" aria-label="Dismiss">
            <x-app.icon name="close" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div
        x-data="{ show: true }"
        x-show="show"
        x-cloak
        role="alert"
        class="rounded-2xl border border-rose-500/25 bg-rose-500/[0.07] px-4 py-3"
    >
        <div class="flex items-start gap-3">
            <span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-rose-500/15 text-rose-600">
                <x-app.icon name="info" />
            </span>
            <div class="flex-1">
                <p class="text-[13px] font-semibold text-rose-700">Please fix the following:</p>
                <ul class="mt-1.5 space-y-1 text-[13px] text-rose-700/90">
                    @foreach ($errors->all() as $error)
                        <li class="flex gap-2"><span class="text-rose-600/70">&bull;</span><span>{{ $error }}</span></li>
                    @endforeach
                </ul>
            </div>
            <button type="button" @click="show = false" class="rounded-md p-1 text-rose-600/60 transition hover:bg-rose-500/10 hover:text-rose-700" aria-label="Dismiss">
                <x-app.icon name="close" />
            </button>
        </div>
    </div>
@endif
