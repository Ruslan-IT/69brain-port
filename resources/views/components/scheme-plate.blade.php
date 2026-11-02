<figure class="scheme">
    <div class="scheme__canvas">
        <svg viewBox="0 0 640 460" role="img" aria-labelledby="plate-title">
            <title id="plate-title">Схема пластины с отверстием</title>
            <defs>
                <radialGradient id="hole-stress" cx="320" cy="214" r="240" gradientUnits="userSpaceOnUse">
                    <stop offset="0.2" stop-color="#10283f"/>
                    <stop offset="0.38" stop-color="#1e4d73"/>
                    <stop offset="0.58" stop-color="#6e9bbb"/>
                    <stop offset="1" stop-color="#e6eef5"/>
                </radialGradient>
                <pattern id="tri-mesh" width="28" height="28" patternUnits="userSpaceOnUse">
                    <path d="M0 28 L28 0 M0 0 H28 V28" fill="none" stroke="#0e2a47" stroke-opacity=".28" stroke-width="1"/>
                </pattern>
                <mask id="plate-hole">
                    <rect x="70" y="48" width="500" height="300" fill="white"/>
                    <circle cx="320" cy="198" r="58" fill="black"/>
                </mask>
            </defs>

            <g mask="url(#plate-hole)">
                <rect x="70" y="48" width="500" height="300" fill="url(#hole-stress)"/>
                <rect x="70" y="48" width="500" height="300" fill="url(#tri-mesh)"/>
            </g>
            <rect x="70" y="48" width="500" height="300" fill="none" stroke="#0e2a47" stroke-width="1.6"/>
            <circle cx="320" cy="198" r="58" fill="#f7f9fb" stroke="#0e2a47" stroke-width="1.5"/>

            <g fill="#ffffff" stroke="#0e2a47" stroke-width="1.1">
                <circle cx="362" cy="158" r="3"/>
                <circle cx="362" cy="238" r="3"/>
                <circle cx="278" cy="158" r="3"/>
                <circle cx="278" cy="238" r="3"/>
            </g>

            <line x1="320" y1="198" x2="430" y2="118" stroke="#0e2a47" stroke-width="1"/>
            <circle cx="320" cy="198" r="2" fill="#0e2a47"/>
            <text x="438" y="116" fill="#0e2a47" font-size="15" font-family="IBM Plex Mono, Segoe UI, sans-serif">d</text>

            <g stroke="#5a6874" stroke-width="1" fill="none">
                <line x1="70" y1="360" x2="70" y2="392"/>
                <line x1="570" y1="360" x2="570" y2="392"/>
                <line x1="70" y1="384" x2="570" y2="384"/>
            </g>
            <text x="320" y="412" text-anchor="middle" fill="#5a6874" font-size="14" font-family="IBM Plex Mono, Segoe UI, sans-serif">b</text>
            <text x="36" y="206" fill="#5a6874" font-size="14" font-family="IBM Plex Mono, Segoe UI, sans-serif">h</text>
        </svg>
    </div>
    <figcaption class="scheme__caption">
        <span>Схема</span>
        <span>Пластина с отверстием</span>
        <span>Не результат расчёта</span>
    </figcaption>
</figure>
