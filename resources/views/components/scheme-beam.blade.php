<figure class="scheme">
    <div class="scheme__canvas">
        <svg viewBox="0 0 640 480" role="img" aria-labelledby="beam-title">
            <title id="beam-title">Схема консольной балки с конечно-элементной сеткой</title>
            <text x="24" y="36" fill="#5a6874" font-size="12" font-family="IBM Plex Mono, Segoe UI, sans-serif">условная шкала</text>
            <g transform="translate(24, 46)">
                <rect width="22" height="8" fill="#e4eef6"/>
                <rect x="22" width="22" height="8" fill="#b7d0e0"/>
                <rect x="44" width="22" height="8" fill="#4d84ae"/>
                <rect x="66" width="22" height="8" fill="#1e4d73"/>
                <rect x="88" width="22" height="8" fill="#10283f"/>
            </g>
            <text x="24" y="70" fill="#5a6874" font-size="11" font-family="IBM Plex Mono, Segoe UI, sans-serif">ниже</text>
            <text x="96" y="70" fill="#5a6874" font-size="11" font-family="IBM Plex Mono, Segoe UI, sans-serif">выше</text>

            <g stroke="#0e2a47" stroke-width="1.15" fill="none">
                <line x1="78" y1="164" x2="108" y2="194"/>
                <line x1="78" y1="190" x2="108" y2="220"/>
                <line x1="78" y1="216" x2="108" y2="246"/>
                <line x1="78" y1="242" x2="108" y2="272"/>
                <line x1="78" y1="268" x2="108" y2="298"/>
                <line x1="78" y1="294" x2="108" y2="324"/>
                <line x1="78" y1="320" x2="108" y2="350"/>
            </g>
            <line x1="108" y1="150" x2="108" y2="352" stroke="#0e2a47" stroke-width="2"/>
            <text x="116" y="142" fill="#0e2a47" font-size="13" font-family="IBM Plex Mono, Segoe UI, sans-serif">заделка</text>

            <g stroke="#0e2a47" stroke-opacity=".45" stroke-width="1" stroke-linejoin="round">
                <polygon points="108,168 108,332 210,158" fill="#10283f"/>
                <polygon points="210,158 108,332 210,292" fill="#1a3d5c"/>
                <polygon points="210,158 210,292 312,150" fill="#1e4d73"/>
                <polygon points="312,150 210,292 312,258" fill="#2f6490"/>
                <polygon points="312,150 312,258 414,146" fill="#4d84ae"/>
                <polygon points="414,146 312,258 414,232" fill="#7eabc6"/>
                <polygon points="414,146 414,232 520,142" fill="#b7d0e0"/>
                <polygon points="520,142 414,232 520,214" fill="#e4eef6"/>
            </g>

            <g fill="#ffffff" stroke="#0e2a47" stroke-width="1.2" class="mesh-nodes">
                <circle class="mesh-node" cx="108" cy="168" r="3.4"/>
                <circle class="mesh-node" cx="210" cy="158" r="3.4"/>
                <circle class="mesh-node" cx="312" cy="150" r="3.4"/>
                <circle class="mesh-node" cx="414" cy="146" r="3.4"/>
                <circle class="mesh-node" cx="520" cy="142" r="3.4"/>
                <circle class="mesh-node" cx="108" cy="332" r="3.4"/>
                <circle class="mesh-node" cx="210" cy="292" r="3.4"/>
                <circle class="mesh-node" cx="312" cy="258" r="3.4"/>
                <circle class="mesh-node" cx="414" cy="232" r="3.4"/>
                <circle class="mesh-node" cx="520" cy="214" r="3.4"/>
            </g>

            <line x1="490" y1="78" x2="490" y2="132" stroke="#0e2a47" stroke-width="1.5"/>
            <polygon points="490,144 484,130 496,130" fill="#0e2a47"/>
            <text x="504" y="96" fill="#0e2a47" font-size="16" font-family="IBM Plex Mono, Segoe UI, sans-serif">P</text>

            <g stroke="#5a6874" stroke-width="1" fill="none">
                <line x1="108" y1="348" x2="108" y2="392"/>
                <line x1="520" y1="230" x2="520" y2="392"/>
                <line x1="108" y1="384" x2="520" y2="384"/>
                <line x1="108" y1="378" x2="108" y2="390"/>
                <line x1="520" y1="378" x2="520" y2="390"/>
            </g>
            <text x="314" y="408" text-anchor="middle" fill="#5a6874" font-size="14" font-family="IBM Plex Mono, Segoe UI, sans-serif">L</text>
        </svg>
    </div>
    <figcaption class="scheme__caption">
        <span>Схема</span>
        <span>Консольная балка, КЭ-сетка</span>
        <span>Не результат расчёта</span>
    </figcaption>
</figure>
