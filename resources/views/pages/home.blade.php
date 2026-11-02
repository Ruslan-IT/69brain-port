@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/beam.css') }}?v={{ filemtime(public_path('css/beam.css')) }}">
@endpush

@if($visualizationAnimated)
    @push('scripts')
        <script type="module" src="{{ asset('js/beam-analysis.js') }}?v={{ filemtime(public_path('js/beam-analysis.js')) }}"></script>
    @endpush
@endif

@section('title', $homePage->documentTitle())
@section('description', $homePage->documentDescription())

@section('content')
    <section class="hero">
        <div class="wrap hero__grid">
            @include('pages.partials.hero-copy')
            <div class="hero__figure">
                <x-hero-analysis />
            </div>
        </div>
    </section>

    @include('pages.partials.home-rest')
@endsection
