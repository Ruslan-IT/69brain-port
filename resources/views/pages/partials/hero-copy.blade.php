<div class="hero__copy">
    @if (filled($homePage->eyebrow))
        <p class="eyebrow">{{ $homePage->eyebrow }}</p>
    @endif
    <h1>{!! nl2br(e($homePage->displayH1()), false) !!}</h1>
    @if (filled($homePage->subtitle))
        <p class="hero__subtitle">{{ $homePage->subtitle }}</p>
    @endif
    @if (filled($homePage->description))
        <p class="lede">{!! nl2br(e($homePage->description), false) !!}</p>
    @endif
    @php($heroHref = \App\Support\SiteUrl::href($homePage->button_url))
    @if (filled($homePage->button_label) && $heroHref)
        <a class="btn" href="{{ $heroHref }}">{{ $homePage->button_label }}</a>
    @endif
</div>
