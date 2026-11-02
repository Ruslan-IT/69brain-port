<div class="analysis" data-analysis>
    <div class="analysis__toolbar" aria-hidden="true">
        <span>FEM Analysis</span>
        <span>Static Structural</span>
        <span class="analysis__mesh-state">Mesh: Active</span>
        <span>Load: <b data-readout="load">12.5 kN</b></span>
        <span>Displacement <b data-readout="disp">6.4 mm</b></span>
        <span>Stress distribution</span>
    </div>

    <div class="analysis__stage">
        @if($visualizationAnimated)
        <svg class="analysis__svg" viewBox="0 0 640 430" role="img" aria-labelledby="analysis-title">
            <title id="analysis-title">Демонстрация статического расчёта консольной балки: нагрузка, изгиб и условные эквивалентные напряжения. Не является результатом расчёта.</title>

            <g class="beam-support" stroke="#0e2a47" fill="none">
                <line x1="78" y1="164" x2="108" y2="194" stroke-width="1.15"/>
                <line x1="78" y1="190" x2="108" y2="220" stroke-width="1.15"/>
                <line x1="78" y1="216" x2="108" y2="246" stroke-width="1.15"/>
                <line x1="78" y1="242" x2="108" y2="272" stroke-width="1.15"/>
                <line x1="78" y1="268" x2="108" y2="298" stroke-width="1.15"/>
                <line x1="78" y1="294" x2="108" y2="324" stroke-width="1.15"/>
                <line x1="78" y1="320" x2="108" y2="350" stroke-width="1.15"/>
                <line x1="108" y1="150" x2="108" y2="352" stroke-width="2"/>
            </g>
            <text x="116" y="138" fill="#0e2a47" font-size="13" font-family="IBM Plex Mono, Segoe UI, sans-serif">заделка</text>

            <g class="beam-mesh" stroke="#0e2a47" stroke-opacity="0.55" stroke-width="1.15" stroke-linejoin="round">
                <polygon class="beam-face" points="108,168 108,332 210,158" fill="#14304c"/>
                <polygon class="beam-face" points="210,158 108,332 210,292" fill="#1a456e"/>
                <polygon class="beam-face" points="210,158 210,292 312,150" fill="#2a6294"/>
                <polygon class="beam-face" points="312,150 210,292 312,258" fill="#3d7eae"/>
                <polygon class="beam-face" points="312,150 312,258 414,146" fill="#6aa3c4"/>
                <polygon class="beam-face" points="414,146 312,258 414,232" fill="#8eb6d0"/>
                <polygon class="beam-face" points="414,146 414,232 520,142" fill="#c5d9e8"/>
                <polygon class="beam-face" points="520,142 414,232 520,214" fill="#e7f1f8"/>
            </g>

            <g class="beam-nodes" fill="#ffffff" stroke="#0e2a47" stroke-width="1.2">
                <circle class="beam-node" data-index="0" cx="108" cy="168" r="3.3"/>
                <circle class="beam-node" data-index="1" cx="210" cy="158" r="3.3"/>
                <circle class="beam-node" data-index="2" cx="312" cy="150" r="3.3"/>
                <circle class="beam-node" data-index="3" cx="414" cy="146" r="3.3"/>
                <circle class="beam-node" data-index="4" cx="520" cy="142" r="3.3"/>
                <circle class="beam-node" data-index="5" cx="108" cy="332" r="3.3"/>
                <circle class="beam-node" data-index="6" cx="210" cy="292" r="3.3"/>
                <circle class="beam-node" data-index="7" cx="312" cy="258" r="3.3"/>
                <circle class="beam-node" data-index="8" cx="414" cy="232" r="3.3"/>
                <circle class="beam-node" data-index="9" cx="520" cy="214" r="3.3"/>
            </g>

            <g class="beam-load" fill="#0e2a47" stroke="#0e2a47">
                <line data-load="shaft" x1="520" y1="78" x2="520" y2="128" stroke-width="1.6"/>
                <polygon data-load="head" points="520,140 514,126 526,126"/>
                <text data-load="label" x="508" y="74" text-anchor="end" font-size="13" font-family="IBM Plex Mono, Segoe UI, sans-serif">P = 12.5 kN</text>
            </g>

            <g class="beam-dimension" stroke="#5a6874" stroke-width="1" fill="none">
                <line x1="108" y1="348" x2="108" y2="392"/>
                <line x1="520" y1="276" x2="520" y2="392"/>
                <line x1="108" y1="384" x2="520" y2="384"/>
                <line x1="108" y1="378" x2="108" y2="390"/>
                <line x1="520" y1="378" x2="520" y2="390"/>
            </g>
            <text x="314" y="412" text-anchor="middle" fill="#5a6874" font-size="14" font-family="IBM Plex Mono, Segoe UI, sans-serif">L</text>
        </svg>
        @else
            <img class="analysis__still" src="{{ asset('images/viz/home-original.png') }}" alt="Демонстрация консольной балки: нагрузка, изгиб и условные напряжения">
        @endif
    </div>

    <div class="analysis__readout">
        <div class="analysis__scale">
            <p class="analysis__scale-title">Эквивалентные напряжения</p>
            <div class="analysis__scale-bar" aria-hidden="true"></div>
            <div class="analysis__scale-ticks" aria-hidden="true">
                <span>0</span>
                <span data-readout="stress-max">84 МПа</span>
            </div>
        </div>
        <p class="analysis__note">Демонстрационные значения, не результат расчёта</p>
    </div>
</div>
