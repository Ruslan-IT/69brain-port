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
        <script type="module" src="{{ asset('js/scenes/strength.js') }}?v={{ filemtime(public_path('js/scenes/strength.js')) }}"></script>
    @endpush
@endif

@section('title', 'Расчёты на прочность и жёсткость — КБ «Прочность»')
@section('description', 'Расчёты на прочность, жёсткость и устойчивость металлических деталей, элементов БПЛА и металлоконструкций. Аналитические методы и метод конечных элементов в ANSYS.')

@section('content')
    <header class="page-hero">
        <div class="wrap page-hero__grid scene-hero">
            <div>
                <p class="eyebrow">Услуги</p>
                <h1>Расчёты на прочность и жёсткость</h1>
                <p class="lede">Выполняем инженерные расчёты деталей и конструкций: на прочность, жёсткость, устойчивость, усталостную прочность конструкций. Владеем как аналитическими методами расчётов из сопротивления материалов и строительной механики, так и методом конечных элементов в ПО ANSYS.</p>
            </div>
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
        </div>
    </header>

    <section class="section">
        <div class="wrap service-layout">
            <nav class="toc" aria-label="Содержание страницы">
                <p class="toc__label">Содержание</p>
                <ol>
                    <li><a href="#metallicheskie-detali"><span>01</span> Расчёт металлических деталей</a></li>
                    <li><a href="#elementy-bpla"><span>02</span> Расчёт элементов БПЛА</a></li>
                    <li><a href="#metallokonstrukcii"><span>03</span> Расчёт металлоконструкций</a></li>
                </ol>
            </nav>

            <div class="sheets">
                <article class="sheet reveal" id="metallicheskie-detali">
                    <div class="sheet__head">
                        <span class="sheet__index">01</span>
                        <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
                            <rect x="6" y="10" width="36" height="28" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="24" cy="24" r="7" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <h2>Расчёт металлических деталей</h2>
                    <p>Расчёт на прочность, жёсткость и устойчивость металлических деталей.</p>
                </article>

                <article class="sheet reveal" id="elementy-bpla">
                    <div class="sheet__head">
                        <span class="sheet__index">02</span>
                        <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M8 8 L20 20 M40 8 L28 20 M8 40 L20 28 M40 40 L28 28" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="18" y="18" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="8" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
                            <circle cx="40" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
                            <circle cx="8" cy="40" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
                            <circle cx="40" cy="40" r="3" fill="none" stroke="currentColor" stroke-width="1.4"/>
                        </svg>
                    </div>
                    <h2>Расчёт элементов БПЛА</h2>
                    <p>Расчёт на прочность, жёсткость и устойчивость элементов БПЛА из металлических, неметаллических и композитных материалов.</p>
                </article>

                <article class="sheet reveal" id="metallokonstrukcii">
                    <div class="sheet__head">
                        <span class="sheet__index">03</span>
                        <svg class="sheet__icon" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M6 38 H42 M10 38 L24 10 L38 38 M17 24 H31 M14 31 H34" fill="none" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <h2>Расчёт металлоконструкций</h2>
                    <p>Расчёт на прочность металлоконструкций.</p>
                </article>
            </div>
        </div>
    </section>

    <x-cta />
@endsection
