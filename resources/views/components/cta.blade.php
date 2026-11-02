@php($ctaHref = \App\Support\SiteUrl::href($homePage->cta_button_url))
@if (filled($homePage->cta_title) || filled($homePage->cta_text) || (filled($homePage->cta_button_label) && $ctaHref))
    <section class="cta">
        <div class="wrap cta__inner">
            <div>
                @if (filled($homePage->cta_title))
                    <h2>{{ $homePage->cta_title }}</h2>
                @endif
                @if (filled($homePage->cta_text))
                    <p>{!! nl2br(e($homePage->cta_text), false) !!}</p>
                @endif
            </div>
            @if (filled($homePage->cta_button_label) && $ctaHref)
                <a class="btn btn--light" href="{{ $ctaHref }}">{{ $homePage->cta_button_label }}</a>
            @endif
        </div>
    </section>
@endif
