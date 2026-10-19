<div class="highlight-grid">
    @foreach(\App\Services\SchoolProfile::highlights() as $highlight)
    <article class="highlight-card" data-reveal>
        @if($highlight['photo'])<img src="{{ $highlight['photo']['src'] }}" alt="{{ $highlight['photo']['alt'] }}" width="{{ $highlight['photo']['width'] }}" height="{{ $highlight['photo']['height'] }}" loading="lazy" decoding="async">@endif
        <div><span class="highlight-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $highlight['title'] }}</h3><p>{{ $highlight['text'] }}</p></div>
    </article>
    @endforeach
</div>
