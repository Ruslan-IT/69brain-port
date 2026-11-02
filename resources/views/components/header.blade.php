@php($headerButton = $siteOptions->headerButton())
<header class="site-header" data-header>
    <div class="site-header__inner wrap">
        <x-logo />

        <nav class="nav" id="site-nav" data-nav aria-label="Основная навигация">
            <ul class="nav__list">
                <li>
                    <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')]) @if(request()->routeIs('home')) aria-current="page" @endif>Главная</a>
                </li>
                @if ($siteServices->isNotEmpty())
                    <li class="nav__dropdown" data-dropdown>
                        <button
                            type="button"
                        id="services-button"
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-controls="services-menu"
                        @class(['nav__services-btn', 'is-active' => $siteServices->contains(fn ($service) => $service->isCurrent())])
                        >
                            Услуги
                            <svg viewBox="0 0 12 8" width="12" height="8" aria-hidden="true">
                                <path d="M1 1.5 L6 6.5 L11 1.5" fill="none" stroke="currentColor" stroke-width="1.4"/>
                            </svg>
                        </button>
                        <div class="nav__panel" id="services-menu">
                            <ul>
                                @foreach ($siteServices as $service)
                                    <li>
                                        <a href="{{ $service->publicUrl() }}" @class(['is-active' => $service->isCurrent()]) @if($service->isCurrent()) aria-current="page" @endif>
                                            <span class="idx">{{ sprintf('%02d', $loop->iteration) }}</span>
                                            <span>{{ $service->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
                <li>
                    <a href="{{ route('contacts') }}" @class(['is-active' => request()->routeIs('contacts')]) @if(request()->routeIs('contacts')) aria-current="page" @endif>Контакты</a>
                </li>
            </ul>
            @if ($headerButton)
                <a class="btn nav__cta" href="{{ $headerButton['url'] }}">{{ $headerButton['label'] }}</a>
            @endif
        </nav>

        @if ($headerButton)
            <a class="btn btn--small header-cta" href="{{ $headerButton['url'] }}">{{ $headerButton['label'] }}</a>
        @endif

        <button
            type="button"
            class="nav-toggle"
            data-nav-toggle
            aria-expanded="false"
            aria-controls="site-nav"
            aria-label="Открыть меню"
        >
            <span></span>
        </button>
    </div>
</header>
