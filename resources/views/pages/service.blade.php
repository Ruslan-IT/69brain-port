@extends('layouts.app')

@if ($service->hasScene())
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/scenes.css') }}?v={{ filemtime(public_path('css/scenes.css')) }}">
    @endpush

    @if ($visualizationAnimated)
        @push('scripts')
            <script src="{{ asset('vendor/gsap/gsap.min.js') }}?v={{ filemtime(public_path('vendor/gsap/gsap.min.js')) }}"></script>
            <script type="importmap">
                {
                    "imports": {
                        "three": "{{ asset('vendor/three/three.module.min.js') }}"
                    }
                }
            </script>
            <script type="module" src="{{ asset('js/scenes/'.$service->key.'.js') }}?v={{ filemtime(public_path('js/scenes/'.$service->key.'.js')) }}"></script>
        @endpush
    @endif
@endif

@section('title', $service->documentTitle())
@section('description', $service->documentDescription())

@section('content')
    <header class="page-hero">
        <div class="wrap {{ $service->hasScene() ? 'page-hero__grid scene-hero' : 'page-hero__intro' }}">
            <div>
                <p class="eyebrow">Услуги</p>
                <h1>{{ $service->displayH1() }}</h1>
                @if (($lead = \App\Support\SafeHtml::clean($service->lead)) !== '')
                    <div class="lede">{!! $lead !!}</div>
                @endif
            </div>
            @if ($service->hasScene())
                @include('pages.partials.service-scene')
            @endif
        </div>
    </header>

    @php($blocks = $service->contentBlocks())
    @if ($blocks !== [])
        <section class="section">
            <div class="wrap service-layout">
                <nav class="toc" aria-label="Содержание страницы">
                    <p class="toc__label">Содержание</p>
                    <ol>
                        @foreach ($blocks as $block)
                            <li>
                                <a href="#{{ $block['anchor'] }}"><span>{{ $block['index'] }}</span> {{ $block['nav'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                @if ($service->key === 'technology')
                    <div>
                        <div class="tech-grid">
                            @foreach ($blocks as $block)
                                @if ($loop->iteration <= 2)
                                    @include('pages.partials.service-block', ['block' => $block])
                                @endif
                            @endforeach
                        </div>
                        @foreach ($blocks as $block)
                            @if ($loop->iteration > 2)
                                @include('pages.partials.service-block', ['block' => $block])
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="sheets">
                        @foreach ($blocks as $block)
                            @include('pages.partials.service-block', ['block' => $block])
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <x-cta />
@endsection
