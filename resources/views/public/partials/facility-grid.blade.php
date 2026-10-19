@php($facilities = \App\Services\SchoolProfile::facilities())
@if($facilities !== [])
<div class="facility-grid">
    @foreach($facilities as $facility)
    <figure class="facility-card" data-reveal>
        <a class="showcase-link" href="{{ $facility['src'] }}" data-lightbox data-caption="{{ $facility['title'] }}" aria-label="Perbesar foto: {{ $facility['title'] }}"><img src="{{ $facility['src'] }}" alt="{{ $facility['title'] }} Ibnu Aqil" width="{{ $facility['width'] }}" height="{{ $facility['height'] }}" loading="lazy" decoding="async"></a>
        <figcaption><strong>{{ $facility['title'] }}</strong><span>{{ $facility['text'] }}</span></figcaption>
    </figure>
    @endforeach
</div>
@endif
