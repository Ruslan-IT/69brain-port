@extends('layouts.app')

@push('styles')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('title', 'ENB — настройки')

@section('content')
    <section class="section">
        <div class="wrap" style="max-width: 40rem;">
            <p class="eyebrow">ENB</p>
            <h1>Настройки сайта</h1>
            @if(session('enb_notice'))
                <p class="form-notice">{{ session('enb_notice') }}</p>
            @endif
            @if(session('enb_error'))
                <p class="field-error">{{ session('enb_error') }}</p>
            @endif
            <form class="form" method="post" action="{{ route('enb.update') }}">
                @csrf
                <fieldset class="field">
                    <legend>Вариант главной страницы</legend>
                    <label><input type="radio" name="homepage_variant" value="original" @checked($homepageVariant === 'original')> Исходный</label>
                    <label><input type="radio" name="homepage_variant" value="new" @checked($homepageVariant === 'new')> Новый, с 3D-сценой</label>
                </fieldset>
                <fieldset class="field">
                    <legend>Режим отображения визуализаций</legend>
                    <label><input type="radio" name="visualization_mode" value="animated" @checked($visualizationMode === 'animated')> С анимацией</label>
                    <label><input type="radio" name="visualization_mode" value="static" @checked($visualizationMode === 'static')> Без анимации, статичные изображения</label>
                </fieldset>
                <button class="btn" type="submit">Сохранить</button>
            </form>
        </div>
    </section>
@endsection
