@props(['name' => 'grid'])

@php
    $paths = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'computer-components' => '<rect x="4" y="4" width="16" height="12" rx="2"/><path d="M9 20h6M12 16v4"/><path d="M8 9h3M8 12h5" stroke-linecap="round"/>',
        'storage-devices' => '<ellipse cx="12" cy="6" rx="7" ry="3"/><path d="M5 6v6c0 1.66 3.13 3 7 3s7-1.34 7-3V6"/><path d="M5 12v6c0 1.66 3.13 3 7 3s7-1.34 7-3v-6"/>',
        'computer-accessories' => '<rect x="2.5" y="6" width="19" height="12" rx="2.5"/><path d="M6 10h.01M9.5 10h.01M13 10h.01M16.5 10h.01M8 14h8" stroke-linecap="round"/>',
        'networking-components' => '<path d="M5 12.5a10 10 0 0 1 14 0"/><path d="M8.5 16a5.5 5.5 0 0 1 7 0"/><circle cx="12" cy="19" r="1.4" fill="currentColor" stroke="none"/>',
        'power-connection' => '<path d="M7 3v5M17 3v5"/><path d="M5 8h14v3a7 7 0 0 1-14 0V8Z"/><path d="M12 18v3" stroke-linecap="round"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5" stroke-linecap="round"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .32 1.77l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.6 1.6 0 0 0-1.77-.32 1.6 1.6 0 0 0-1 1.47V21a2 2 0 1 1-4 0v-.11a1.6 1.6 0 0 0-1.05-1.46 1.6 1.6 0 0 0-1.77.32l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.6 1.6 0 0 0 .32-1.77 1.6 1.6 0 0 0-1.47-1H3a2 2 0 1 1 0-4h.11a1.6 1.6 0 0 0 1.46-1.05 1.6 1.6 0 0 0-.32-1.77l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.6 1.6 0 0 0 1.77.32H9a1.6 1.6 0 0 0 1-1.47V3a2 2 0 1 1 4 0v.11a1.6 1.6 0 0 0 1 1.47 1.6 1.6 0 0 0 1.77-.32l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.6 1.6 0 0 0-.32 1.77V9a1.6 1.6 0 0 0 1.47 1H21a2 2 0 1 1 0 4h-.11a1.6 1.6 0 0 0-1.47 1Z"/>',
        'user' => '<circle cx="12" cy="8" r="3.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke-linecap="round"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>',
        'plus' => '<path d="M12 5v14M5 12h14" stroke-linecap="round"/>',
        'trash' => '<path d="M4 7h16M10 7V5h4v2M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12" stroke-linecap="round" stroke-linejoin="round"/>',
        'edit' => '<path d="M4 20h4l10-10a2.83 2.83 0 1 0-4-4L4 16v4Z" stroke-linejoin="round"/>',
        'eye' => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
        'filter' => '<path d="M3 5h18l-7 8v6l-4 2v-8L3 5Z" stroke-linejoin="round"/>',
        'chevron-left' => '<path d="m14.5 6-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/>',
        'chevron-right' => '<path d="m9.5 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>',
        'close' => '<path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/>',
        'layers' => '<path d="m12 3 8.5 4.5L12 12 3.5 7.5 12 3Z" stroke-linejoin="round"/><path d="m3.5 12.5 8.5 4.5 8.5-4.5" stroke-linejoin="round"/><path d="m3.5 16.5 8.5 4.5 8.5-4.5" stroke-linejoin="round"/>',
        'tag' => '<path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9-9-9Z" stroke-linejoin="round"/><circle cx="7.5" cy="7.5" r="1.3"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>',
        'refresh' => '<path d="M20 11a8 8 0 1 0-.6 4" stroke-linecap="round"/><path d="M20 4v7h-7" stroke-linecap="round" stroke-linejoin="round"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5" stroke-linecap="round"/><circle cx="12" cy="7.8" r="1" fill="currentColor" stroke="none"/>',
    ];

    $path = $paths[$name] ?? $paths['grid'];
@endphp

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-[18px] w-[18px]" aria-hidden="true">
    {!! $path !!}
</svg>
