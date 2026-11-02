@extends('layouts.app')

@push('styles')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('title', 'ENB — вход')

@section('content')
    <section class="section">
        <div class="wrap" style="max-width: 28rem;">
            <p class="eyebrow">ENB</p>
            <h1>Настройки сайта</h1>
            @if(session('enb_error'))
                <p class="field-error">{{ session('enb_error') }}</p>
            @endif
            <form class="form" method="post" action="{{ route('enb.login') }}">
                @csrf
                <div class="field">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
                <button class="btn" type="submit">Войти</button>
            </form>
        </div>
    </section>
@endsection
