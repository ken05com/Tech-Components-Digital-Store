@props([
    'action',
    'method' => 'DELETE',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Yes, confirm',
    'triggerLabel' => null,
    'icon' => 'trash',
    'compact' => false,
    'tone' => 'rose',
])

@php
    $tones = [
        'rose' => [
            'trigger' => 'border-rose-500/25 bg-rose-500/[0.07] text-rose-600 hover:border-rose-500/45 hover:bg-rose-500/[0.12]',
            'badge' => 'bg-rose-500/12 text-rose-600 ring-rose-500/25',
            'confirm' => 'bg-rose-500 hover:bg-rose-400',
        ],
        'accent' => [
            'trigger' => 'border-accent/25 bg-accent/[0.08] text-accent hover:border-accent/45 hover:bg-accent/[0.14]',
            'badge' => 'bg-accent/12 text-accent ring-accent/25',
            'confirm' => 'bg-accent hover:bg-accent-soft',
        ],
        'emerald' => [
            'trigger' => 'border-emerald-500/25 bg-emerald-500/[0.07] text-emerald-600 hover:border-emerald-500/45 hover:bg-emerald-500/[0.12]',
            'badge' => 'bg-emerald-500/12 text-emerald-600 ring-emerald-500/25',
            'confirm' => 'bg-emerald-500 hover:bg-emerald-400',
        ],
    ];
    $style = $tones[$tone] ?? $tones['rose'];
    $id = 'confirm-'.uniqid();
@endphp

<div x-data="{ open: false }" x-cloak>
    <button
        type="button"
        @click="open = true"
        @class([
            ! $compact
                ? 'inline-flex h-9 items-center gap-2 rounded-xl border px-3.5 text-[12.5px] font-semibold transition '.$style['trigger']
                : 'grid h-7 w-7 place-items-center rounded-lg border transition '.$style['trigger'],
        ])
        @if ($compact) title="{{ $triggerLabel ?? $title }}" aria-label="{{ $triggerLabel ?? $title }}" @endif
    >
        <x-app.icon :name="$icon" />
        @unless ($compact)
            {{ $triggerLabel ?? $confirmLabel }}
        @endunless
    </button>

    <div x-show="open" class="fixed inset-0 z-[65] grid place-items-center p-4" role="dialog" aria-modal="true" :aria-labelledby="'{{ $id }}-title'">
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
            class="relative w-full max-w-md overflow-hidden rounded-2xl border border-line bg-panel shadow-2xl"
        >
            <div class="flex gap-3.5 p-5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl ring-1 {{ $style['badge'] }}">
                    <x-app.icon :name="$icon" />
                </span>
                <div>
                    <h3 id="{{ $id }}-title" class="text-[15px] font-semibold text-ink">{{ $title }}</h3>
                    <p class="mt-1.5 text-[12.5px] leading-relaxed text-muted">{!! $message !!}</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-line bg-line/30 px-5 py-3.5">
                <button type="button" @click="open = false" class="h-9 rounded-xl px-3.5 text-[12.5px] font-semibold text-muted transition hover:bg-line/40 hover:text-ink">
                    Cancel
                </button>
                <form method="POST" action="{{ $action }}">
                    @csrf
                    @method($method)
                    <button type="submit" class="h-9 rounded-xl px-4 text-[12.5px] font-semibold text-white transition active:scale-[0.98] {{ $style['confirm'] }}">
                        {{ $confirmLabel }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
