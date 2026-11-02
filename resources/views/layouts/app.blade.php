<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', $siteOptions->default_seo_description ?: 'Инженерное бюро КБ «Прочность»: расчёты на прочность, компьютерное моделирование и статистический анализ.')">
    <meta name="theme-color" content="#0e2a47">
    <meta name="format-detection" content="telephone=no">
    <title>@yield('title', $siteOptions->default_seo_title ?: $siteName)</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Manrope:wght@400;500;600;700&family=Unbounded:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    @stack('styles')
    <script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>
</head>
<body>
    <a class="skip-link" href="#main">К содержанию</a>
    <x-header />
    <main id="main">
        @yield('content')
    </main>
    <x-footer />
    @stack('scripts')
</body>
</html>
