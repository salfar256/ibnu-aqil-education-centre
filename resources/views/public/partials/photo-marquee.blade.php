@php($marqueeRows = array_values(array_filter(\App\Services\SchoolProfile::marquee())))
@if($marqueeRows !== [])
<div class="photo-marquee" role="region" aria-label="Foto kegiatan santri dan siswa Ibnu Aqil">
    @foreach($marqueeRows as $rowIndex => $row)
    <div class="photo-marquee-row {{ $rowIndex % 2 ? 'is-reverse' : '' }}">
        <div class="photo-marquee-track">
            @foreach([false, true] as $duplicate)
            <ul class="photo-marquee-group" @if($duplicate) aria-hidden="true" @endif>
                @foreach($row as $photo)
                <li><a href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" @if($duplicate) tabindex="-1" @endif aria-label="Perbesar foto: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $duplicate ? '' : $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"><span class="photo-marquee-caption"><small>{{ $photo['tag'] }}</small>{{ $photo['caption'] }}</span></a></li>
                @endforeach
            </ul>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif
