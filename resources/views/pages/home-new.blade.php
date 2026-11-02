@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/scenes.css') }}?v={{ filemtime(public_path('css/scenes.css')) }}">
@endpush

@if($visualizationAnimated)
    @push('scripts')
        <script src="{{ asset('vendor/gsap/gsap.min.js') }}?v={{ filemtime(public_path('vendor/gsap/gsap.min.js')) }}"></script>
        <script type="importmap">
            { "imports": { "three": "{{ asset('vendor/three/three.module.min.js') }}" } }
        </script>
        <script type="module" src="{{ asset('js/scenes/home.js') }}?v={{ filemtime(public_path('js/scenes/home.js')) }}"></script>
    @endpush
@endif

@section('title', $homePage->documentTitle())
@section('description', $homePage->documentDescription())

@section('content')
    <section class="hero hero--stage">
        <div class="wrap hero-stage__grid">
            @include('pages.partials.hero-copy')
            <x-scene name="home">
                <x-slot:toolbar>
                    <span>Static structural</span>
                    <span>Plate with hole</span>
                    <span>Stress</span>
                </x-slot:toolbar>
                <x-slot:fallback>
                    <img src="{{ asset('images/viz/home-new.png') }}" alt="Демонстрация пластины с отверстием: сетка и условные напряжения">
                </x-slot:fallback>
            </x-scene>
        </div>
    </section>

    @include('pages.partials.home-rest')
@endsection
