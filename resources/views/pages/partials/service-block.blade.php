<article @class(['sheet', 'reveal', 'sheet--extra' => $block['extra_sheet']]) id="{{ $block['anchor'] }}">
    <div class="sheet__head">
        <span class="sheet__index">{{ $block['index'] }}</span>
        @if ($block['icon'])
            <x-sheet-icon :name="$block['icon']" />
        @endif
    </div>
    @if ($block['title'] !== '')
        <h2>{{ $block['title'] }}</h2>
    @endif
    @if (($body = \App\Support\SafeHtml::clean($block['body'])) !== '')
        <div class="sheet__text">{!! $body !!}</div>
    @endif
    @if (($extra = \App\Support\SafeHtml::clean($block['extra'])) !== '')
        <div class="sheet__text">{!! $extra !!}</div>
    @endif
    @if ($block['link_url'])
        <a class="text-link" href="{{ $block['link_url'] }}">{{ $block['link_label'] }}</a>
    @endif
</article>
