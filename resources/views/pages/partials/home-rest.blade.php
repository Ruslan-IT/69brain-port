@php($works = $homePage->workItems())
@if ($works !== [] || filled($homePage->works_title))
    <section class="section" id="chto-my-delaem">
        <div class="wrap">
            <div class="section__head">
                <p class="eyebrow">01</p>
                @if (filled($homePage->works_title))
                    <h2>{{ $homePage->works_title }}</h2>
                @endif
            </div>

            @if ($works !== [])
                <div class="work-list">
                    @foreach ($works as $item)
                        <article class="work-item reveal">
                            <div class="work-item__meta">
                                <span class="work-item__index">{{ $item['index'] }}</span>
                                @if ($item['title'] !== '')
                                    <h3>{{ $item['title'] }}</h3>
                                @endif
                            </div>
                            <div class="work-item__body">
                                @if (($body = \App\Support\SafeHtml::clean($item['body'])) !== '')
                                    {!! $body !!}
                                @endif
                                @if ($item['link_url'])
                                    <a class="text-link" href="{{ $item['link_url'] }}">{{ $item['link_label'] }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

@if (filled($homePage->who_title) || trim(strip_tags((string) $homePage->who_text)) !== '' || trim(strip_tags((string) $homePage->who_extra)) !== '')
    <section class="section section--muted">
        <div class="wrap who">
            <div>
                <p class="eyebrow">02</p>
                @if (filled($homePage->who_title))
                    <h2>{{ $homePage->who_title }}</h2>
                @endif
            </div>
            <div class="who__copy">
                @if (($who = \App\Support\SafeHtml::clean($homePage->who_text)) !== '')
                    <div class="who__statement">{!! $who !!}</div>
                @endif
                @if (($extra = \App\Support\SafeHtml::clean($homePage->who_extra)) !== '')
                    <div class="who__extra">{!! $extra !!}</div>
                @endif
            </div>
        </div>
    </section>
@endif

@if ($siteServices->isNotEmpty() || filled($homePage->directions_title))
    <section class="section" id="napravleniya">
        <div class="wrap">
            <div class="section__head">
                <p class="eyebrow">03</p>
                @if (filled($homePage->directions_title))
                    <h2>{{ $homePage->directions_title }}</h2>
                @endif
            </div>
            @if ($siteServices->isNotEmpty())
                <div class="direction-grid">
                    @foreach ($siteServices as $service)
                        <a class="direction reveal" href="{{ $service->publicUrl() }}">
                            <span class="direction__index">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <h3>{{ $service->title }}</h3>
                            @if (filled($service->excerpt))
                                <p>{{ $service->excerpt }}</p>
                            @endif
                            @if (filled($service->card_link_label))
                                <span class="direction__go">{{ $service->card_link_label }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

<x-cta />
