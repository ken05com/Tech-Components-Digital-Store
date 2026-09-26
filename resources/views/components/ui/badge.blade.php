@props([
    'label',
    'value' => null,
    'tone' => 'slate',
    'dot' => false,
])

@php
    $tones = [
        'slate' => 'bg-white/[0.05] text-slate-300 ring-line',
        'accent' => 'bg-accent/12 text-accent ring-accent/25',
        'emerald' => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20',
        'sky' => 'bg-sky-500/10 text-sky-400 ring-sky-500/20',
        'violet' => 'bg-violet-500/10 text-violet-400 ring-violet-500/20',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $tones[$tone] ?? $tones['slate'] }}">
    @if ($dot)<span class="h-1.5 w-1.5 rounded-full bg-current"></span>@endif
    {{ $value ? $value.' · ' : '' }}{{ $label }}
</span>
