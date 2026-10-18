@extends('layouts.public')
@section('title', 'Ibnu Aqil Education Centre — Ruang Belajar dan Berbagi')
@section('description', 'Kenali MI, SMP, SMA, SMK, Pondok Pesantren, dan KBIHU Ibnu Aqil. Temukan informasi lembaga serta akses Portal Pengimbasan untuk sekolah mitra.')
@php
    $photos = \App\Services\PublicPhotos::all();
    $heroPhoto = $photos['masjid'] ?? null;
    $unitLabels = ['mi' => 'MI', 'smp' => 'SMP', 'sma' => 'SMA', 'smk' => 'SMK', 'pesantren' => 'PP', 'kbihu' => 'KBIHU'];
    $ribbon = ['MI', 'SMP', 'SMA', 'SMK', 'PESANTREN', 'KBIHU'];
@endphp
@if($heroPhoto)
@push('head')<link rel="preload" as="image" href="{{ $heroPhoto['src'] }}">@endpush
@endif
@section('content')
<section class="home-hero @if($heroPhoto) home-hero-photo @endif">
    <span class="hero-glow hero-glow-one" aria-hidden="true"></span><span class="hero-glow hero-glow-two" aria-hidden="true"></span>
    <div class="container hero-grid">
        <div class="hero-copy" data-reveal><p class="eyebrow"><span></span> Selamat datang di Ibnu Aqil</p><h1>Ruang untuk<br>belajar, <em>bertumbuh,</em><br>dan berbagi.</h1><p class="hero-description">Kenali unit pendidikan, pesantren, dan KBIHU Ibnu Aqil di bawah Yayasan Haji Agus Salim Bogor. Temukan informasi dan ruang berbagi dalam satu tempat.</p><div class="hero-actions"><a class="button button-glow" href="#unit-ibnu-aqil">Jelajahi Ibnu Aqil <span aria-hidden="true">↗</span></a><a class="text-link" href="{{ route('public.page', 'tentang') }}">Tentang lembaga <span aria-hidden="true">→</span></a></div><div class="hero-footnote"><span class="small-star" aria-hidden="true">✳</span><p>Terhubung dengan Ibnu Aqil,<br>melalui pendidikan dan pengimbasan.</p></div></div>
        @if($heroPhoto)
        <figure class="hero-photo" data-reveal data-tilt>
            <div class="hero-photo-frame"><img src="{{ $heroPhoto['src'] }}" alt="{{ $heroPhoto['alt'] }}" width="{{ $heroPhoto['width'] }}" height="{{ $heroPhoto['height'] }}" fetchpriority="high" decoding="async"></div>
            <figcaption class="hero-chip hero-chip-top"><span class="chip-dot" aria-hidden="true"></span>{{ $heroPhoto['caption'] }}</figcaption>
            <div class="hero-chip hero-chip-bottom"><strong>6</strong><span>unit pendidikan<br>dan layanan</span></div>
            <span class="hero-photo-star" aria-hidden="true">✳</span>
        </figure>
        @else
        <div class="hero-art" role="img" aria-label="Ilustrasi geometris lengkung dan buku untuk ruang belajar Ibnu Aqil" data-reveal>
            <span class="art-label">ILUSTRASI · RUANG BELAJAR</span><span class="art-orbit orbit-one"></span><span class="art-orbit orbit-two"></span><span class="art-sun"></span><div class="art-arch"><div class="art-arch-inner"><span class="art-overline">IBNU AQIL</span><span class="art-calligraphy" aria-hidden="true">اقرأ</span><span class="art-word">Mulai dari<br><em>rasa ingin tahu.</em></span><div class="art-rule"></div><span class="art-bottom">BELAJAR &nbsp; / &nbsp; BERTUMBUH &nbsp; / &nbsp; BERBAGI</span></div></div><div class="art-book" aria-hidden="true"><span></span><span></span></div><span class="art-star" aria-hidden="true">✳</span><span class="art-dot-grid" aria-hidden="true"></span><span class="art-caption">Sebuah ruang. Banyak jalan untuk bertumbuh.</span>
        </div>
        @endif
    </div>
</section>
<div class="unit-ribbon" role="region" aria-label="Unit Ibnu Aqil"><div class="marquee"><div class="marquee-track">@foreach([false, true] as $duplicate)<div class="marquee-group" @if($duplicate) aria-hidden="true" @endif>@foreach($ribbon as $label)<span>{{ $label }}</span><i aria-hidden="true">✦</i>@endforeach</div>@endforeach</div></div></div>
<section class="section section-units" id="unit-ibnu-aqil">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Mengenal lebih dekat</p><h2>Temukan ruang<br><em>belajar Anda.</em></h2></div><p>Dari pendidikan sekolah hingga pesantren dan KBIHU, jelajahi setiap bagian dari Ibnu Aqil.</p></div>
        <div class="unit-grid">
            @foreach($units as $unit)
            <a class="unit-card unit-card-{{ $unit->slug }}" href="{{ route('public.unit', $unit->slug) }}" data-reveal data-spotlight><div class="unit-card-top"><span class="unit-symbol" aria-hidden="true">{{ $unitLabels[$unit->slug] ?? 'IA' }}</span><span class="unit-arrow" aria-hidden="true">↗</span></div><div><h3>{{ $unit->title }}</h3>@if($unit->summary)<p>{{ $unit->summary }}</p>@endif</div><span class="unit-card-link">Lihat profil <span aria-hidden="true">→</span></span></a>
            @endforeach
        </div>
    </div>
</section>
@if($photos !== [])
<section class="section section-showcase" aria-labelledby="showcase-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Suasana Ibnu Aqil</p><h2 id="showcase-heading">Lingkungan yang<br><em>menumbuhkan.</em></h2></div><p>Sekilas lingkungan Ibnu Aqil, Yayasan Haji Agus Salim Bogor. Pilih gambar untuk memperbesar.</p></div>
        <div class="showcase-grid showcase-count-{{ min(count($photos), 3) }}">
            @foreach($photos as $photo)
            <figure class="showcase-item" data-reveal><a class="showcase-link" href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar gambar: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"><span class="showcase-zoom" aria-hidden="true">＋</span></a><figcaption><span class="showcase-tag">{{ $photo['tag'] }}</span>{{ $photo['caption'] }}</figcaption></figure>
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="portal-feature"><div class="container portal-feature-grid"><div class="portal-visual" aria-hidden="true" data-reveal data-tilt><div class="portal-tile tile-back"><span>ILMU</span><i>↗</i></div><div class="portal-tile tile-front"><span class="tile-mark">↗</span><span>Berbagi<br><em>praktik baik.</em></span><small>PORTAL PENGIMBASAN</small></div><div class="portal-visual-star">✳</div></div><div class="portal-feature-copy" data-reveal><p class="eyebrow">Untuk sekolah mitra</p><h2>Pengetahuan tumbuh<br><em>saat dibagikan.</em></h2><p>Akses video, foto, ebook, dan modul pengimbasan yang dibagikan untuk sekolah Anda, dalam satu ruang yang terkelola.</p><div class="portal-types"><span>Video</span><span>Foto</span><span>Ebook</span><span>Modul</span></div><a class="button button-cream" href="{{ route('portal.index') }}">Masuk Portal Pengimbasan <span aria-hidden="true">↗</span></a><p class="portal-note">Akses menggunakan akun yang diberikan oleh pengelola.</p></div></div></section>
<section class="section section-news"><div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Cerita dari Ibnu Aqil</p><h2>Berita & <em>kegiatan.</em></h2></div><a class="text-link" href="{{ route('public.news') }}">Semua berita <span aria-hidden="true">↗</span></a></div>
    @if($news->isNotEmpty())<div class="news-grid">@foreach($news as $article)@include('public.partials.article-card', ['article' => $article])@endforeach</div>
    @else<div class="empty-state empty-inline" data-reveal><span class="empty-mark" aria-hidden="true">↗</span><div><h3>Ruang cerita Ibnu Aqil</h3><p>Berita dan kegiatan dapat dibaca di sini setelah diterbitkan oleh pengelola.</p></div><a class="text-link" href="{{ route('public.page', 'tentang') }}">Kenali lembaga <span aria-hidden="true">→</span></a></div>@endif
</div></section>
<section class="explore-strip"><div class="container"><a href="{{ route('public.page', 'fasilitas') }}" data-reveal><span class="explore-number">01</span><span>Fasilitas & program</span><i aria-hidden="true">↗</i></a><a href="{{ route('public.page', 'prestasi') }}" data-reveal><span class="explore-number">02</span><span>Prestasi</span><i aria-hidden="true">↗</i></a><a href="{{ route('public.gallery') }}" data-reveal><span class="explore-number">03</span><span>Galeri Ibnu Aqil</span><i aria-hidden="true">↗</i></a></div></section>
<section class="contact-banner"><div class="container"><div data-reveal><p class="eyebrow">Langkah berikutnya</p><h2>Mari mengenal<br><em>Ibnu Aqil lebih dekat.</em></h2></div><div data-reveal><p>Temukan informasi pendaftaran atau hubungi pengelola untuk informasi lebih lanjut.</p><div class="hero-actions"><a class="button button-glow" href="{{ route('public.page', 'pendaftaran') }}">Info pendaftaran <span aria-hidden="true">↗</span></a><a class="text-link" href="{{ route('public.page', 'kontak') }}">Hubungi kami <span aria-hidden="true">→</span></a></div></div></div></section>
@endsection
