@php($trophy = \App\Services\SchoolProfile::photo('juara-akuntansi'))
<div class="achievement-layout">
    @if($trophy)<figure class="achievement-photo" data-reveal><a class="showcase-link" href="{{ $trophy['src'] }}" data-lightbox data-caption="{{ $trophy['caption'] }}" aria-label="Perbesar foto: {{ $trophy['caption'] }}"><img src="{{ $trophy['src'] }}" alt="{{ $trophy['alt'] }}" width="{{ $trophy['width'] }}" height="{{ $trophy['height'] }}" loading="lazy" decoding="async"></a></figure>@endif
    <ol class="achievement-list">
        @foreach(array_slice(\App\Services\SchoolProfile::ACHIEVEMENTS, 0, $limit ?? null) as [$title, $level, $year])
        <li data-reveal><span class="achievement-medal" aria-hidden="true">★</span><div><strong>{{ $title }}</strong><span>{{ $level }} · {{ $year }}</span></div></li>
        @endforeach
    </ol>
</div>
