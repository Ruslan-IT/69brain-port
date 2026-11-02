@extends('layouts.app')

@section('title', $siteOptions->contactsDocumentTitle())
@section('description', $siteOptions->contactsDocumentDescription())

@section('content')
    <header class="page-hero">
        <div class="wrap page-hero__intro">
            @if (filled($siteOptions->contacts_title))
                <p class="eyebrow">{{ $siteOptions->contacts_title }}</p>
            @endif
            <h1>{{ $siteOptions->contactsH1() }}</h1>
            @if (filled($siteOptions->contacts_lead))
                <p class="lede">{!! nl2br(e($siteOptions->contacts_lead), false) !!}</p>
            @endif
        </div>
    </header>

    <section class="section">
        <div class="wrap contacts-grid">
            @if ($siteOptions->hasContactDetails())
                <div class="contact-card">
                    <h2>Контактные данные</h2>
                    <dl class="contact-slots">
                        @if (filled($siteOptions->phone))
                            <div class="contact-slot">
                                <dt>Телефон</dt>
                                <dd class="is-set">
                                    @if ($siteOptions->phoneLink())
                                        <a href="{{ $siteOptions->phoneLink() }}">{{ $siteOptions->phone }}</a>
                                    @else
                                        {{ $siteOptions->phone }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if (filled($siteOptions->email))
                            <div class="contact-slot">
                                <dt>Электронная почта</dt>
                                <dd class="is-set">
                                    @if ($siteOptions->emailLink())
                                        <a href="{{ $siteOptions->emailLink() }}">{{ $siteOptions->email }}</a>
                                    @else
                                        {{ $siteOptions->email }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if (filled($siteOptions->address))
                            <div class="contact-slot">
                                <dt>Адрес</dt>
                                <dd class="is-set">{!! nl2br(e($siteOptions->address), false) !!}</dd>
                            </div>
                        @endif
                        @if (filled($siteOptions->requisites))
                            <div class="contact-slot">
                                <dt>Реквизиты</dt>
                                <dd class="is-set">{!! nl2br(e($siteOptions->requisites), false) !!}</dd>
                            </div>
                        @endif
                    </dl>
                    @if (($extra = \App\Support\SafeHtml::clean($siteOptions->contact_extra)) !== '')
                        <div class="contact-extra">{!! $extra !!}</div>
                    @endif
                </div>
            @endif

            <div class="contact-card">
                <h2>Обратная связь</h2>
                <p class="form-note">Приём сообщений с сайта пока не подключён. Поля можно проверить, но обращение не будет доставлено.</p>

                @if (session('form_notice'))
                    <div class="form-notice" role="status">{{ session('form_notice') }}</div>
                @endif

                @if ($errors->any())
                    <div class="form-errors" role="alert">
                        <p>Проверьте поля формы.</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="form" method="post" action="{{ route('contacts.store') }}" novalidate>
                    @csrf
                    <div class="field">
                        <label for="name">Имя</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            maxlength="120"
                            autocomplete="name"
                            required
                            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                        >
                        @error('name')
                            <p class="field-error" id="name-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="contact">Телефон или электронная почта</label>
                        <input
                            id="contact"
                            name="contact"
                            type="text"
                            value="{{ old('contact') }}"
                            maxlength="180"
                            autocomplete="on"
                            required
                            @error('contact') aria-invalid="true" aria-describedby="contact-error" @enderror
                        >
                        @error('contact')
                            <p class="field-error" id="contact-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="message">Сообщение</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            maxlength="5000"
                            required
                            @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="field-error" id="message-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="btn btn--submit" type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </section>
@endsection
