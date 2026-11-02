{{-- Текст страницы берётся из базы и выводится через pages/service.blade.php. --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/scenes.css') }}?v={{ filemtime(public_path('css/scenes.css')) }}">
@endpush

@if($visualizationAnimated)
    @push('scripts')
        <script src="{{ asset('vendor/gsap/gsap.min.js') }}?v={{ filemtime(public_path('vendor/gsap/gsap.min.js')) }}"></script>
        <script type="importmap">
            {
                "imports": {
                    "three": "{{ asset('vendor/three/three.module.min.js') }}"
                }
            }
        </script>
        <script type="module" src="{{ asset('js/scenes/statistics.js') }}?v={{ filemtime(public_path('js/scenes/statistics.js')) }}"></script>
    @endpush
@endif

@section('title', 'Статистические расчёты — КБ «Прочность»')
@section('description', 'Статистические расчёты: проверка гипотез, качество данных, регрессия, дисперсионный анализ, анализ измерительных систем и контрольные карты Шухарта.')

@section('content')
    <header class="page-hero">
        <div class="wrap page-hero__grid scene-hero">
            <div>
                <p class="eyebrow">Услуги</p>
                <h1>Статистические расчёты</h1>
                <p class="lede">Выполняем статистические расчёты (<span lang="en">not so big data</span>, массивы размерностью до 1000 столбцов на 500&nbsp;000 строк): анализ качества данных, проверка гипотез, поиск корреляций, регрессионный анализ, построение контрольных карт Шухарта и пр.</p>
            </div>
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
        </div>
    </header>

    <section class="section">
        <div class="wrap service-layout">
            <nav class="toc" aria-label="Содержание страницы">
                <p class="toc__label">Содержание</p>
                <ol>
                    <li><a href="#bazovaya-statistika"><span>01</span> Базовая статистика</a></li>
                    <li><a href="#regressiya"><span>02</span> Регрессионный и дисперсионный анализ</a></li>
                    <li><a href="#msa"><span>03</span> Анализ измерительных систем</a></li>
                    <li><a href="#kontrolnye-karty"><span>04</span> Контрольные карты</a></li>
                </ol>
            </nav>

            <div class="sheets">
                <article class="sheet reveal" id="bazovaya-statistika">
                    <div class="sheet__head">
                        <span class="sheet__index">01</span>
                    </div>
                    <h2>Базовая статистика</h2>
                    <ul class="check-list">
                        <li>Проверка статистических гипотез.</li>
                        <li>Поиск выбросов.</li>
                        <li>Анализ качества данных.</li>
                    </ul>
                </article>

                <article class="sheet reveal" id="regressiya">
                    <div class="sheet__head">
                        <span class="sheet__index">02</span>
                    </div>
                    <h2>Регрессионный и дисперсионный анализ</h2>
                    <ul class="check-list">
                        <li>Регрессионный анализ.</li>
                        <li>Дисперсионный анализ (ANOVA).</li>
                        <li>Поиск зависимостей и корреляций.</li>
                    </ul>
                </article>

                <article class="sheet reveal" id="msa">
                    <div class="sheet__head">
                        <span class="sheet__index">03</span>
                    </div>
                    <h2>Анализ измерительных систем (MSA)</h2>
                    <ul class="check-list">
                        <li>Повторяемость.</li>
                        <li>Воспроизводимость.</li>
                    </ul>
                </article>

                <article class="sheet reveal" id="kontrolnye-karty">
                    <div class="sheet__head">
                        <span class="sheet__index">04</span>
                    </div>
                    <h2>Контрольные карты</h2>
                    <ul class="check-list">
                        <li>Контрольные карты Шухарта.</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <x-cta />
@endsection
