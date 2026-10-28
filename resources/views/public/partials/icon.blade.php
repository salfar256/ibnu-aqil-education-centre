@php
    // Simple line icons (24×24, stroke = currentColor). Usage: @include('public.partials.icon', ['name' => 'phone'])
    $paths = [
        'whatsapp' => '<path d="M20 11.8a8 8 0 0 1-11.9 7L4 20l1.2-4A8 8 0 1 1 20 11.8Z"/><path d="M8.6 8.4c-.6 1.8 1.8 5.6 4.7 6.7 1.1.4 2-.8 2-1.1l-2-1-.7.7c-1.2-.7-2.2-1.6-2.7-2.7l.6-.7-1-2Z" fill="currentColor" stroke="none"/>',
        'phone' => '<path d="M5 4h3.5l1.7 4.3-2.2 1.4a11 11 0 0 0 6.3 6.3l1.4-2.2L20 15.5V19a1.5 1.5 0 0 1-1.6 1.5A16 16 0 0 1 3.5 5.6 1.5 1.5 0 0 1 5 4Z"/>',
        'pin' => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.6"/>',
        'form' => '<rect x="5" y="3.5" width="14" height="17" rx="2"/><path d="M9 3.5h6v3H9zM8.5 11h7M8.5 14.5h7M8.5 18h4"/>',
        'school' => '<path d="m3 9 9-5 9 5-9 5-9-5Z"/><path d="M7 11.3V16c0 1.4 2.2 3 5 3s5-1.6 5-3v-4.7M21 9v6"/>',
        'wallet' => '<rect x="3" y="6" width="18" height="13" rx="2.5"/><path d="M3 10h18M16 14.5h2"/><path d="M6 6 15.5 3 17 6"/>',
        'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4M8 14h2M14 14h2M8 17h2"/>',
        'home' => '<path d="m4 11 8-7 8 7"/><path d="M6 9.5V20h4.5v-5.5h3V20H18V9.5"/>',
        'mosque' => '<path d="M12 3v2M7 12a5 5 0 0 1 10 0"/><path d="M5 12h14v8H5zM10 20v-3.5a2 2 0 0 1 4 0V20M2.5 20V9M21.5 20V9M2.5 9l1-2 1 2M19.5 9l1-2 1 2M2.5 20h19"/>',
        'book' => '<path d="M12 6.5C10 5 7.5 4.5 4 4.5v13c3.5 0 6 .5 8 2 2-1.5 4.5-2 8-2v-13c-3.5 0-6 .5-8 2Z"/><path d="M12 6.5v13"/>',
        'bed' => '<path d="M3 18V6M3 14h18v4M21 14v-2.5A2.5 2.5 0 0 0 18.5 9H11v5"/><circle cx="7" cy="11" r="1.8"/>',
        'medal' => '<circle cx="12" cy="15" r="5"/><path d="m9 3 3 5 3-5M7.5 3h9M12 13l.8 1.6 1.7.2-1.2 1.2.3 1.7-1.6-.8-1.6.8.3-1.7-1.2-1.2 1.7-.2Z"/>',
        'ball' => '<circle cx="12" cy="12" r="8.5"/><path d="m12 7.5 3.4 2.5-1.3 4h-4.2l-1.3-4Z"/><path d="M12 3.5v4M15.4 10l4.3-1.4M14.1 14l2.6 3.6M9.9 14l-2.6 3.6M8.6 10 4.3 8.6"/>',
        'star' => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8-4.3-4.1 5.9-.9Z"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'check' => '<circle cx="12" cy="12" r="8.5"/><path d="m8.2 12.2 2.6 2.6 5-5.2"/>',
        'user' => '<circle cx="12" cy="8" r="3.8"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'people' => '<circle cx="9" cy="8.5" r="3.2"/><path d="M3 19.5a6 6 0 0 1 12 0M15.5 5.5a3 3 0 0 1 0 6M17.5 14a5.5 5.5 0 0 1 3.5 5.5"/>',
        'camera' => '<path d="M4 8h3.2L9 5.5h6L16.8 8H20a1 1 0 0 1 1 1v9.5a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13.5" r="3.5"/>',
        'help' => '<circle cx="12" cy="12" r="8.5"/><path d="M9.6 9.4a2.5 2.5 0 1 1 3.4 2.3c-.6.3-1 .8-1 1.5v.6"/><circle cx="12" cy="16.8" r=".6" fill="currentColor"/>',
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'chev' => '<path d="m9 6 6 6-6 6"/>',
        'sparkle' => '<path d="M12 3c.6 4.2 2.8 6.4 7 7-4.2.6-6.4 2.8-7 7-.6-4.2-2.8-6.4-7-7 4.2-.6 6.4-2.8 7-7Z"/>',
        'compass' => '<circle cx="12" cy="12" r="8.5"/><path d="m15.5 8.5-2 5-5 2 2-5Z"/>',
        'shield' => '<path d="M12 3.5 5 6v5.5c0 4.3 3 7.7 7 9 4-1.3 7-4.7 7-9V6Z"/><path d="m9 12 2 2 4-4"/>',
    ];
    $svg = $paths[$name] ?? $paths['sparkle'];
@endphp
<svg class="ia-icon {{ $class ?? '' }}" viewBox="0 0 24 24" width="{{ $size ?? 24 }}" height="{{ $size ?? 24 }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $svg !!}</svg>
