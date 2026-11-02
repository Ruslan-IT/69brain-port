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
        <script type="module" src="{{ asset('js/scenes/technology.js') }}?v={{ filemtime(public_path('js/scenes/technology.js')) }}"></script>
    @endpush
@endif

@section('title', 'Технологические расчёты по листовой штамповке и лазерной резке — КБ «Прочность»')
@section('description', 'Моделирование холодной листовой штамповки в Deform-3D и подготовка комплекта DXF для лазерной резки: раскрой, файлы для станка, затраты времени и материала.')

@section('content')
    <header class="page-hero">
        <div class="wrap page-hero__grid scene-hero">
            <div>
                <p class="eyebrow">Услуги</p>
                <h1>Технологические расчёты по листовой штамповке и лазерной резке</h1>
                <p class="lede">Выполняем технологическую подготовку комплекта файлов (*.DXF) и документов для лазерной резки: отрисовка 3D-модели, раскрой материала, расчёт затрат времени и материала.</p>
            </div>
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
        </div>
    </header>

    <section class="section">
        <div class="wrap service-layout">
            <nav class="toc" aria-label="Содержание страницы">
                <p class="toc__label">Содержание</p>
                <ol>
                    <li><a href="#listovaya-shtampovka"><span>01</span> Листовая штамповка</a></li>
                    <li><a href="#lazernaya-rezka"><span>02</span> Лазерная резка</a></li>
                    <li><a href="#gibka"><span>03</span> Операции гибки</a></li>
                </ol>
            </nav>

            <div>
                <div class="tech-grid">
                    <article class="sheet reveal" id="listovaya-shtampovka">
                        <div class="sheet__head">
                            <span class="sheet__index">01</span>
                            <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M8 14 H40 L34 34 H14 Z" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M14 34 L18 40 H30 L34 34" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </div>
                        <h2>Листовая штамповка</h2>
                        <p>Моделирование процессов холодной листовой штамповки в среде Deform-3D.</p>
                    </article>

                    <article class="sheet reveal" id="lazernaya-rezka">
                        <div class="sheet__head">
                            <span class="sheet__index">02</span>
                            <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
                                <rect x="8" y="12" width="32" height="24" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M14 30 L24 16 L34 30" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="24" cy="16" r="1.6" fill="currentColor"/>
                            </svg>
                        </div>
                        <h2>Лазерная резка</h2>
                        <p>Расчёты для лазерной резки:</p>
                        <ul class="check-list">
                            <li>Раскрой материала.</li>
                            <li>Подготовка файлов для станка.</li>
                            <li>Подготовка комплекта файлов DXF.</li>
                            <li>Расчёт затрат времени и материала.</li>
                        </ul>
                    </article>
                </div>

                <article class="sheet sheet--extra reveal" id="gibka">
                    <div class="sheet__head">
                        <span class="sheet__index">03</span>
                    </div>
                    <h2>Дополнительная возможность</h2>
                    <p>Технологический расчёт операций гибки при предоставлении необходимых технологических данных (коэффициенты пружинения, чертежи штамповой оснастки и пр.).</p>
                </article>
            </div>
        </div>
    </section>

    <x-cta />
@endsection
