@extends('layouts.public')
@section('title', 'Fasilitas & Kegiatan — Ibnu Aqil Education Center')
@section('description', 'Foto fasilitas dan kegiatan siswa-siswi Sekolah Ibnu Aqil: masjid, ruang belajar, laboratorium, asrama, security 24 jam, serta kegiatan MI, SMP, SMA, dan SMK.')
@php
    use App\Services\Facilities;
    $groups = Facilities::groups();
    $activities = Facilities::activities();
@endphp
@section('content')
<section class="ia-page-hero">
    <div class="container">
        <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <span aria-current="page">Fasilitas & kegiatan</span></nav>
        <h1>Fasilitas & kegiatan</h1>
        <p>Lihat langsung tempat putra-putri Anda belajar, beribadah, dan berkegiatan.</p>
        <nav class="ia-jump" aria-label="Lompat ke bagian">
            @foreach($groups as $i => $g)<a href="#fasilitas-{{ $i }}">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 18]) {{ \Illuminate\Support\Str::before($g['title'], ' (') }}</a>@endforeach
            @foreach($activities as $i => $g)<a href="#kegiatan-{{ $i }}">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 18]) {{ $g['title'] }}</a>@endforeach
        </nav>
    </div>
</section>

@foreach($groups as $i => $g)
<section class="ia-section ia-fac-group" id="fasilitas-{{ $i }}" aria-labelledby="fac-h-{{ $i }}">
    <div class="container">
        <h2 id="fac-h-{{ $i }}" class="ia-title ia-title-icon">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 30]) {{ $g['title'] }}</h2>
        <div class="ia-fac-grid">
            @foreach($g['photos'] as $photo)
            <figure class="ia-fac-item">
                <a href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar foto: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"></a>
                <figcaption>{{ $photo['caption'] }}</figcaption>
            </figure>
            @endforeach
        </div>
    </div>
</section>
@endforeach

@foreach($activities as $i => $g)
<section class="ia-section ia-fac-group ia-activity-group" id="kegiatan-{{ $i }}" aria-labelledby="keg-h-{{ $i }}">
    <div class="container">
        <h2 id="keg-h-{{ $i }}" class="ia-title ia-title-icon">@include('public.partials.icon', ['name' => $g['icon'], 'size' => 30]) {{ $g['title'] }}</h2>
        <div class="ia-fac-grid">
            @foreach($g['photos'] as $photo)
            <figure class="ia-fac-item">
                <a href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar foto: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $photo['caption'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"></a>
                <figcaption>{{ $photo['caption'] }}</figcaption>
            </figure>
            @endforeach
        </div>
    </div>
</section>
@endforeach

@if($page->body)
<section class="ia-section ia-fac-list">
    <div class="container"><details class="faq-item"><summary><span>Daftar lengkap fasilitas</span><i aria-hidden="true">+</i></summary><div class="faq-answer"><div class="content-body">{!! nl2br(e($page->body)) !!}</div></div></details></div>
</section>
@endif
@endsection
