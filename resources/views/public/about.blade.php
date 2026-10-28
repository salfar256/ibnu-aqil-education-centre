@extends('layouts.public')
@section('title', 'Tentang Sekolah Ibnu Aqil — Ibnu Aqil Education Center')
@section('description', $page->summary ?: 'Sekolah Ibnu Aqil di Laladon, Ciomas, Bogor, berdiri sejak 1992.')
@php
    $founder = is_file(public_path('assets/foto/web/pembina-kh-agus-salim.webp'))
        ? ['src' => asset('assets/foto/web/pembina-kh-agus-salim.webp'), 'size' => getimagesize(public_path('assets/foto/web/pembina-kh-agus-salim.webp'))]
        : null;
    $ppdbUrl = \App\Services\WhatsApp::link($settings ?? [], 'Saya ingin mengenal Sekolah Ibnu Aqil lebih dekat.');
    $facts = [['star', '1992', 'Berdiri sejak'], ['school', '4 sekolah', 'MI · SMP · SMA · SMK'], ['medal', 'A', 'Akreditasi SMP, SMA, SMK'], ['ball', '25+', 'Ekstrakurikuler']];
@endphp
@section('content')
{{-- Pembuka: pesan pendiri bersama fotonya --}}
<section class="ia-founder-hero">
    <div class="container ia-founder-inner">
        <div class="ia-founder-copy">
            <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <span aria-current="page">Tentang</span></nav>
            <p class="ia-founder-arabic" lang="ar" dir="rtl">المحافظة على القديم الصالح والأخذ بالجديد الأصلح</p>
            <h1>“Menjaga tradisi yang baik, <span>mengambil yang lebih baik.</span>”</h1>
            <p class="ia-founder-name"><strong>KH. Agus Salim, S.Sos., M.M.</strong><span>Pendiri Sekolah Ibnu Aqil</span></p>
        </div>
        @if($founder)
        <figure class="ia-founder-photo"><img src="{{ $founder['src'] }}" alt="KH. Agus Salim, S.Sos., M.M., pendiri Sekolah Ibnu Aqil" width="{{ $founder['size'][0] }}" height="{{ $founder['size'][1] }}" fetchpriority="high" decoding="async"></figure>
        @endif
    </div>
</section>

<section class="ia-section">
    <div class="container ia-about-grid">
        <div class="ia-about-text">
            <h2 class="ia-title">Sekolah Ibnu Aqil</h2>
            @if($page->body)<div class="content-body">{!! nl2br(e($page->body)) !!}</div>@endif
        </div>
        <ul class="ia-facts">
            @foreach($facts as [$icon, $big, $small])
            <li>@include('public.partials.icon', ['name' => $icon, 'size' => 26])<strong>{{ $big }}</strong><span>{{ $small }}</span></li>
            @endforeach
        </ul>
    </div>
</section>

<section class="ia-section ia-about-units" aria-labelledby="about-units-heading">
    <div class="container">
        <div class="ia-head"><h2 id="about-units-heading" class="ia-title">Pilih sekolah</h2></div>
        @include('public.partials.unit-cards')
    </div>
</section>

<section class="ia-contact">
    <div class="container ia-contact-inner">
        <div><h2>Ingin melihat langsung?</h2><p>Atur kunjungan ke kampus atau tanya apa saja lewat WhatsApp.</p></div>
        <div class="ia-contact-actions">
            @if($ppdbUrl)<a class="ia-btn ia-btn-wa ia-btn-block" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) Chat WhatsApp PPDB</a>@endif
            <a class="ia-btn ia-btn-light ia-btn-block" href="{{ route('public.page', 'fasilitas') }}">@include('public.partials.icon', ['name' => 'camera']) Lihat fasilitas</a>
        </div>
    </div>
</section>
@endsection
