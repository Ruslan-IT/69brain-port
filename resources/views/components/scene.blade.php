<figure class="scene{{ $visualizationAnimated ? '' : ' is-static' }}" data-scene="{{ $name }}">
    @isset($toolbar)
        <div class="scene__toolbar">{{ $toolbar }}</div>
    @endisset
    <div class="scene__viewport">
        @if($visualizationAnimated)
            <canvas data-scene-canvas aria-hidden="true"></canvas>
            <div class="scene__fallback">
                {{ $fallback }}
            </div>
        @else
            <img class="scene__still" src="{{ asset('images/viz/'.$name.'.png') }}" alt="{{ $stillAlt ?? 'Демонстрационная визуализация, не результат расчёта' }}">
        @endif
    </div>
    <div class="scene__readout">
        <p class="scene__phase" data-phase aria-hidden="true"></p>
        <p class="scene__note">{{ $note ?? 'Демонстрационная визуализация, не результат расчёта' }}</p>
    </div>
</figure>
