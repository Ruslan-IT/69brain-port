<a {{ $attributes->merge(['class' => 'logo', 'href' => $siteOptions->logoHref()]) }}>
    <svg class="logo__mark" viewBox="0 0 32 32" width="32" height="32" aria-hidden="true">
        <rect x="1.5" y="1.5" width="29" height="29" fill="none" stroke="currentColor" stroke-width="1.4"/>
        <path d="M8 23.5 L16 8.5 L24 23.5 Z" fill="none" stroke="currentColor" stroke-width="1.3"/>
        <path d="M8 23.5 L24 23.5 L16 16.1 Z" fill="currentColor" opacity=".16"/>
        <circle cx="16" cy="8.5" r="1.35" fill="currentColor"/>
        <circle cx="8" cy="23.5" r="1.35" fill="currentColor"/>
        <circle cx="24" cy="23.5" r="1.35" fill="currentColor"/>
    </svg>
    @if (filled($siteOptions->logo_text))
        <span class="logo__name">{{ $siteOptions->logo_text }}</span>
    @endif
</a>
