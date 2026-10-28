@extends('layouts.public')
@section('title', 'Fasilitas & Ekstrakurikuler — Ibnu Aqil Education Centre')
@section('description', 'Foto fasilitas Sekolah Ibnu Aqil: masjid, ruang belajar, laboratorium, lapangan olahraga, asrama, dan ekstrakurikuler.')
@php
    use App\Services\{Facilities, Units};
    $groups = Facilities::groups();
    $ekskul = Units::ekskul(array_keys(Units::EKSKUL));
    $activityPhotos = array_values(array_filter([
        ['assets/foto/fasilitas/ekskul-silat.webp', 'Pencak Silat'],
        ['assets/foto/fasilitas/ekskul-handball.webp', 'Handball'],
        ['assets/foto/fasilitas/ekskul-pramuka.webp', 'Pramuka'],
        ['assets/foto/web/futsal.jpg', 'Futsal'],
        ['assets/foto/web/karate.jpg', 'Karate'],
        ['assets/foto/web/math-club.jpg', 'Math Club'],
        ['assets/foto/web/science-club.jpg', 'Sains Club'],
        ['assets/foto/fasilitas/ekskul-silat-2.webp', 'Pencak Silat'],
    ], fn ($p) => is_file(public_path($p[0]))));
@endphp
@section('content')
<section class="ia-page-hero">
    <div class="container">
        <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <span aria-current="page">Fasilitas</span></nav>
        <h1>Fasilitas & ekstrakurikuler</h1>
        <p>Lihat langsung tempat putra-putri Anda belajar, beribadah, dan berolahraga.</p>
        <nav class="ia-jump" aria-label="Lompat ke bagian">
            @foreach($groups as $i => $g)<a href="#fasilitas-{{ $i }}">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 18]) {{ \Illuminate\Support\Str::before($g['title'], ' (') }}</a>@endforeach
            <a href="#ekstrakurikuler">@include('public.partials.icon', ['name' => 'star', 'size' => 18]) Ekskul</a>
        </nav>
    </div>
</section>

@foreach($groups as $i => $g)
<section class="ia-section ia-fac-group" id="fasilitas-{{ $i }}" aria-labelledby="fac-h-{{ $i }}">
    <div class="container">
        <h2 id="fac-h-{{ $i }}" class="ia-title ia-title-icon">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 30]) {{ $g['title'] }}</h2>
        <div class="ia-fac-grid">
            @foreach($g['photos'] as $photo)
            <figure class="ia-fac-item {{ $photo['height'] > $photo['width'] ? 'is-tall' : '' }}">
                <a href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar foto: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"></a>
                <figcaption>{{ $photo['caption'] }}</figcaption>
            </figure>
            @endforeach
        </div>
    </div>
</section>
@endforeach

<section class="ia-section ia-ekskul-section" id="ekstrakurikuler" aria-labelledby="ekskul-heading">
    <div class="container">
        <h2 id="ekskul-heading" class="ia-title ia-title-icon">@include('public.partials.icon', ['name' => 'star', 'size' => 30]) Ekstrakurikuler</h2>
        <p class="ia-note">Pilihan ekskul berbeda di setiap sekolah. Lihat halaman masing-masing sekolah untuk daftarnya.</p>
        <ul class="ia-ekskul">
            @foreach($ekskul as $e)
            <li>@if($e['src'])<img src="{{ $e['src'] }}" alt="" width="96" height="96" loading="lazy" decoding="async">@endif<span>{{ $e['label'] }}</span></li>
            @endforeach
        </ul>
        @if($activityPhotos !== [])
        <div class="ia-fac-grid ia-fac-grid-square">
            @foreach($activityPhotos as [$src, $caption])
            @php($imgSize = getimagesize(public_path($src)))
            <figure class="ia-fac-item"><a href="{{ asset($src) }}" data-lightbox data-caption="{{ $caption }}" aria-label="Perbesar foto: {{ $caption }}"><img src="{{ asset($src) }}" alt="Kegiatan {{ $caption }} di Ibnu Aqil" width="{{ $imgSize[0] }}" height="{{ $imgSize[1] }}" loading="lazy" decoding="async"></a><figcaption>{{ $caption }}</figcaption></figure>
            @endforeach
        </div>
        @endif
    </div>
</section>

@if($page->body)
<section class="ia-section ia-fac-list">
    <div class="container"><details class="faq-item"><summary><span>Daftar lengkap fasilitas</span><i aria-hidden="true">+</i></summary><div class="faq-answer"><div class="content-body">{!! nl2br(e($page->body)) !!}</div></div></details></div>
</section>
@endif
@endsection
