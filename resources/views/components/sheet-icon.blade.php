@props(['name' => null])

@if ($name === 'plate')
    <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
        <rect x="6" y="10" width="36" height="28" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <circle cx="24" cy="24" r="7" fill="none" stroke="currentColor" stroke-width="1.5"/>
    </svg>
@elseif ($name === 'drone')
    <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
        <path d="M8 8 L20 20 M40 8 L28 20 M8 40 L20 28 M40 40 L28 28" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <rect x="18" y="18" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <circle cx="8" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
        <circle cx="40" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
        <circle cx="8" cy="40" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
        <circle cx="40" cy="40" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
    </svg>
@elseif ($name === 'frame')
    <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
        <path d="M6 38 H42 M10 38 L24 10 L38 38 M17 24 H31 M14 31 H34" fill="none" stroke="currentColor" stroke-width="1.5"/>
    </svg>
@elseif ($name === 'stamp')
    <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
        <path d="M8 14 H40 L34 34 H14 Z" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <path d="M14 34 L18 40 H30 L34 34" fill="none" stroke="currentColor" stroke-width="1.5"/>
    </svg>
@elseif ($name === 'laser')
    <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
        <rect x="8" y="12" width="32" height="24" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <path d="M14 30 L24 16 L34 30" fill="none" stroke="currentColor" stroke-width="1.5"/>
        <circle cx="24" cy="16" r="1.6" fill="currentColor"/>
    </svg>
@endif
