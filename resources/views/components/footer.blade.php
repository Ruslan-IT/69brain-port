@php($footerLinks = $siteOptions->footerLinkItems())
<footer class="site-footer">
    <div class="wrap site-footer__grid">
        <div>
            <x-logo />
            @if (filled($siteOptions->footer_lead))
                <p class="site-footer__lead">{{ $siteOptions->footer_lead }}</p>
            @endif
        </div>

        @if ($footerLinks !== [])
            <nav aria-label="Страницы">
                <p class="site-footer__title">Страницы</p>
                <ul>
                    @foreach ($footerLinks as $link)
                        <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if ($siteServices->isNotEmpty())
            <nav aria-label="Услуги в подвале">
                <p class="site-footer__title">Услуги</p>
                <ul>
                    @foreach ($siteServices as $service)
                        <li><a href="{{ $service->publicUrl() }}">{{ $service->title }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if (filled($siteOptions->phone) || filled($siteOptions->email) || filled($siteOptions->address))
            <div>
                <p class="site-footer__title">Контакты</p>
                <ul>
                    @if (filled($siteOptions->phone))
                        <li>
                            @if ($siteOptions->phoneLink())
                                <a href="{{ $siteOptions->phoneLink() }}">{{ $siteOptions->phone }}</a>
                            @else
                                {{ $siteOptions->phone }}
                            @endif
                        </li>
                    @endif
                    @if (filled($siteOptions->email))
                        <li>
                            @if ($siteOptions->emailLink())
                                <a href="{{ $siteOptions->emailLink() }}">{{ $siteOptions->email }}</a>
                            @else
                                {{ $siteOptions->email }}
                            @endif
                        </li>
                    @endif
                    @if (filled($siteOptions->address))
                        <li>{!! nl2br(e($siteOptions->address), false) !!}</li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
    <div class="site-footer__bottom">
        <div class="wrap">
            <p>{{ $siteOptions->copyrightLine() }}</p>
        </div>
    </div>
</footer>
