@props(['name' => null, 'class' => 'h-5 w-5', 'filled' => false])

@php
    $solid = $filled ? 'currentColor' : 'none';
    $stroke = $filled ? 0 : 1.8;
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ $solid }}" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}" aria-hidden="true" focusable="false">
    @switch($name)
        @case('search')
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
            @break

        @case('user')
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
            @break

        @case('heart')
            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
            @break

        @case('menu')
            <line x1="4" x2="20" y1="6" y2="6"></line>
            <line x1="4" x2="20" y1="12" y2="12"></line>
            <line x1="4" x2="20" y1="18" y2="18"></line>
            @break

        @case('close')
            <path d="M18 6 6 18"></path>
            <path d="m6 6 12 12"></path>
            @break

        @case('chevron-down')
            <path d="m6 9 6 6 6-6"></path>
            @break

        @case('chevron-left')
            <path d="m15 18-6-6 6-6"></path>
            @break

        @case('arrow-right')
            <path d="M5 12h14"></path>
            <path d="m12 5 7 7-7 7"></path>
            @break

        @case('arrow-up-right')
            <path d="M7 7h10v10"></path>
            <path d="M7 17 17 7"></path>
            @break

        @case('globe')
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
            <path d="M2 12h20"></path>
            @break

        @case('sparkles')
            <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0Z"></path>
            <path d="M20 3v4"></path>
            <path d="M22 5h-4"></path>
            @break

        @case('cpu')
            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
            <rect x="9" y="9" width="6" height="6"></rect>
            <path d="M15 2v2"></path>
            <path d="M15 20v2"></path>
            <path d="M2 15h2"></path>
            <path d="M2 9h2"></path>
            <path d="M20 15h2"></path>
            <path d="M20 9h2"></path>
            <path d="M9 2v2"></path>
            <path d="M9 20v2"></path>
            @break

        @case('database')
            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
            <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
            @break

        @case('router')
            <rect width="20" height="8" x="2" y="13" rx="2"></rect>
            <path d="M6 17h.01"></path>
            <path d="M10 17h.01"></path>
            <path d="M15 9v4"></path>
            <path d="M18.5 7.5a5.5 5.5 0 0 0-7 0"></path>
            @break

        @case('headphones')
            <path d="M4 13v-1a8 8 0 0 1 16 0v1"></path>
            <rect x="4" y="13" width="4" height="7" rx="1.5"></rect>
            <rect x="16" y="13" width="4" height="7" rx="1.5"></rect>
            @break

        @case('headset')
            <path d="M3 12a9 9 0 0 1 18 0"></path>
            <path d="M4 12v6a2 2 0 0 0 2 2h2v-8H4"></path>
            <path d="M20 12v6a2 2 0 0 1-2 2h-2v-8h4"></path>
            <path d="M4 12v1"></path>
            <path d="M20 12v1"></path>
            @break

        @case('smartwatch')
            <rect x="6" y="7" width="12" height="10" rx="2.5"></rect>
            <path d="M9 7V4h6v3"></path>
            <path d="M9 17v3h6v-3"></path>
            @break

        @case('usb')
            <rect x="9" y="2" width="6" height="5" rx="1"></rect>
            <path d="M10 7v6l-4 5a2 2 0 0 0 1.7 3.2h8.6A2 2 0 0 0 18 18l-4-5V7"></path>
            <circle cx="12" cy="15" r="1"></circle>
            @break

        @case('tag')
            <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"></path>
            <circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle>
            @break

        @case('truck')
            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
            <path d="M15 18H9"></path>
            <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
            <circle cx="17" cy="18" r="2"></circle>
            <circle cx="7" cy="18" r="2"></circle>
            @break

        @case('shield-check')
            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
            <path d="m9 12 2 2 4-4"></path>
            @break

        @case('refresh')
            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
            <path d="M3 3v5h5"></path>
            @break

        @case('monitor')
            <rect width="20" height="13" x="2" y="3" rx="2"></rect>
            <path d="M8 21h8"></path>
            <path d="M12 17v4"></path>
            @break

        @case('plane')
            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path>
            @break

        @case('gamepad')
            <line x1="6" x2="10" y1="12" y2="12"></line>
            <line x1="8" x2="8" y1="10" y2="14"></line>
            <line x1="15" x2="15.01" y1="13" y2="13"></line>
            <line x1="18" x2="18.01" y1="11" y2="11"></line>
            <rect width="20" height="12" x="2" y="6" rx="2"></rect>
            @break

        @case('gift')
            <rect x="3" y="8" width="18" height="4" rx="1"></rect>
            <path d="M12 8v13"></path>
            <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
            <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
            @break

        @case('mail')
            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
            @break

        @case('cart')
            <circle cx="9" cy="20" r="1.4"></circle>
            <circle cx="18" cy="20" r="1.4"></circle>
            <path d="M2 3h2.2l2.4 12.5a1.6 1.6 0 0 0 1.57 1.3h8.66a1.6 1.6 0 0 0 1.57-1.28L20.5 7H5.2"></path>
            @break
    @endswitch
</svg>