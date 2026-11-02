@if ($service->key === 'strength')
    <x-scene name="strength">
        <x-slot:toolbar>
            <span>Static structural</span>
            <span>Plate with hole</span>
            <span>Mesh: shell</span>
            <span>Tension</span>
        </x-slot:toolbar>
        <x-slot:fallback>
            <x-scheme-plate />
        </x-slot:fallback>
    </x-scene>
@elseif ($service->key === 'statistics')
    <x-scene name="statistics">
        <x-slot:toolbar>
            <span>Regression</span>
            <span>Scatter</span>
            <span>Correlation</span>
        </x-slot:toolbar>
        <x-slot:fallback>
            <x-scheme-chart />
        </x-slot:fallback>
    </x-scene>
@elseif ($service->key === 'technology')
    <x-scene name="technology">
        <x-slot:toolbar>
            <div class="scene__switch" role="tablist" aria-label="Технологический процесс">
                <button type="button" role="tab" data-mode="stamp" class="is-on" aria-selected="true">Штамповка</button>
                <button type="button" role="tab" data-mode="laser" aria-selected="false">Лазерная резка</button>
            </div>
        </x-slot:toolbar>
        <x-slot:fallback>
            <x-scheme-nesting />
        </x-slot:fallback>
    </x-scene>
@endif
