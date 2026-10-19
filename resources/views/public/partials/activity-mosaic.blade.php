@if($photos !== [])
<div class="activity-mosaic {{ count($photos) >= 6 ? 'activity-mosaic-full' : 'activity-mosaic-compact' }}">
    @foreach($photos as $photo)
    <figure class="activity-item" data-reveal>
        <a class="showcase-link" href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar foto: {{ $photo['caption'] }}">
            <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async">
            <span class="showcase-zoom" aria-hidden="true">＋</span>
        </a>
        <figcaption><span class="showcase-tag">{{ $photo['tag'] }}</span>{{ $photo['caption'] }}</figcaption>
    </figure>
    @endforeach
</div>
@endif
