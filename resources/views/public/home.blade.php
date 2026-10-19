@extends('layouts.public')
@section('title', 'Ibnu Aqil Education Centre — MI, SMP, SMA, SMK, Pesantren Bogor')
@section('description', 'Ibnu Aqil, Yayasan Haji Agus Salim Bogor: MI, SMP, SMA Boarding School, SMK, Pondok Pesantren, dan KBIHU di Laladon, Ciomas. Informasi PPDB 2026/2027 melalui WhatsApp PPDB.')
@php
    $heroPhoto = \App\Services\PublicPhotos::get('masjid') ?? \App\Services\SchoolProfile::photo('santriwati-kitab');
    $heroInset = \App\Services\SchoolProfile::photo('tadarus-bersama');
    $unitLabels = ['mi' => 'MI', 'smp' => 'SMP', 'sma' => 'SMA', 'smk' => 'SMK', 'pesantren' => 'PP', 'kbihu' => 'KBIHU'];
    $ribbon = ['MI', 'SMP', 'SMA BOARDING SCHOOL', 'SMK', 'PONDOK PESANTREN', 'KBIHU'];
    $ppdbUrl = preg_match('~^https://wa\.me/[0-9]{9,15}$~', (string) ($settings['whatsapp_url'] ?? '')) ? $settings['whatsapp_url'] : null;
    $campusPhotos = \App\Services\PublicPhotos::all();
@endphp
@if($heroPhoto)
@push('head')<link rel="preload" as="image" href="{{ $heroPhoto['src'] }}">@endpush
@endif
@section('content')
<section class="home-hero @if($heroPhoto) home-hero-photo @endif">
    <span class="hero-glow hero-glow-one" aria-hidden="true"></span><span class="hero-glow hero-glow-two" aria-hidden="true"></span>
    <div class="container hero-grid">
        <div class="hero-copy" data-reveal><p class="eyebrow"><span></span> PPDB 2026/2027 · Yayasan Haji Agus Salim Bogor</p><h1>Tumbuh berilmu,<br><em>berakhlak,</em><br>dan siap melangkah.</h1><p class="hero-description">MI, SMP, SMA Boarding School, SMK, Pondok Pesantren, dan KBIHU dalam satu lingkungan yang membiasakan ibadah, menghidupkan tradisi kitab kuning, dan mengasah bakat putra-putri Anda.</p><div class="hero-actions">@if($ppdbUrl)<a class="button button-glow" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp PPDB <span aria-hidden="true">↗</span></a>@else<a class="button button-glow" href="{{ route('public.page', 'pendaftaran') }}">Info pendaftaran <span aria-hidden="true">↗</span></a>@endif<a class="text-link" href="#unit-ibnu-aqil">Jelajahi unit <span aria-hidden="true">→</span></a></div><div class="hero-footnote"><span class="small-star" aria-hidden="true">✳</span><p>Kampus Laladon, Ciomas, Bogor.<br>Asrama putra dan putri tersedia.</p></div></div>
        @if($heroPhoto)
        <figure class="hero-photo" data-reveal data-tilt>
            <div class="hero-photo-frame"><img src="{{ $heroPhoto['src'] }}" alt="{{ $heroPhoto['alt'] }}" width="{{ $heroPhoto['width'] }}" height="{{ $heroPhoto['height'] }}" fetchpriority="high" decoding="async"></div>
            @if($heroInset)<div class="hero-inset"><img src="{{ $heroInset['src'] }}" alt="{{ $heroInset['alt'] }}" width="{{ $heroInset['width'] }}" height="{{ $heroInset['height'] }}" decoding="async"></div>@endif
            <figcaption class="hero-chip hero-chip-top"><span class="chip-dot" aria-hidden="true"></span>{{ $heroPhoto['caption'] }}</figcaption>
            <div class="hero-chip hero-chip-bottom"><strong>A</strong><span>akreditasi SMP,<br>SMA, dan SMK</span></div>
            <span class="hero-photo-star" aria-hidden="true">✳</span>
        </figure>
        @endif
    </div>
</section>
<div class="unit-ribbon" role="region" aria-label="Unit Ibnu Aqil"><div class="marquee"><div class="marquee-track">@foreach([false, true] as $duplicate)<div class="marquee-group" @if($duplicate) aria-hidden="true" @endif>@foreach($ribbon as $label)<span>{{ $label }}</span><i aria-hidden="true">✦</i>@endforeach</div>@endforeach</div></div></div>
@include('public.partials.stats-band')
<section class="section section-units" id="unit-ibnu-aqil">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Pilihan jenjang</p><h2>Satu lingkungan,<br><em>banyak jalan bertumbuh.</em></h2></div><p>Dari madrasah ibtidaiyah hingga SMK dan pesantren, setiap unit berada dalam satu kawasan dengan pembinaan ibadah yang sama.</p></div>
        <div class="unit-grid">
            @foreach($units as $unit)
            <a class="unit-card unit-card-{{ $unit->slug }}" href="{{ route('public.unit', $unit->slug) }}" data-reveal data-spotlight><div class="unit-card-top">@if($unitLogo = \App\Services\PublicPhotos::unitLogo($unit->slug))<img class="unit-card-logo" src="{{ asset($unitLogo) }}" alt="" width="64" height="64" loading="lazy" decoding="async">@else<span class="unit-symbol" aria-hidden="true">{{ $unitLabels[$unit->slug] ?? 'IA' }}</span>@endif<span class="unit-arrow" aria-hidden="true">↗</span></div><div><h3>{{ $unit->title }}</h3>@if($unit->summary)<p>{{ $unit->summary }}</p>@endif</div><span class="unit-card-link">Lihat profil <span aria-hidden="true">→</span></span></a>
            @endforeach
        </div>
    </div>
</section>
<section class="section section-highlights" aria-labelledby="highlights-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Mengapa Ibnu Aqil</p><h2 id="highlights-heading">Bekal ilmu, adab,<br><em>dan ibadah.</em></h2></div><p>Enam hal yang membuat keluarga mempercayakan pendidikan putra-putrinya kepada Ibnu Aqil.</p></div>
        @include('public.partials.highlights')
    </div>
</section>
<section class="section section-activities" aria-labelledby="activities-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Kehidupan di Ibnu Aqil</p><h2 id="activities-heading">Hari-hari yang<br><em>hidup dan bermakna.</em></h2></div><a class="text-link" href="{{ route('public.gallery') }}">Lihat galeri <span aria-hidden="true">↗</span></a></div>
        @include('public.partials.activity-mosaic', ['photos' => \App\Services\SchoolProfile::activities(9)])
    </div>
</section>
@include('public.partials.founder')
<section class="section section-achievements" aria-labelledby="achievements-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Prestasi siswa</p><h2 id="achievements-heading">Berprestasi di<br><em>dalam dan luar kelas.</em></h2></div><a class="text-link" href="{{ route('public.achievements') }}">Semua prestasi <span aria-hidden="true">↗</span></a></div>
        @include('public.partials.achievements', ['limit' => 5])
    </div>
</section>
@if($campusPhotos !== [])
<section class="section section-showcase" aria-labelledby="showcase-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Suasana kampus</p><h2 id="showcase-heading">Lingkungan yang<br><em>menumbuhkan.</em></h2></div><a class="text-link" href="{{ route('public.page', 'fasilitas') }}">Fasilitas lengkap <span aria-hidden="true">↗</span></a></div>
        <div class="showcase-grid showcase-count-{{ min(count($campusPhotos), 3) }}">
            @foreach($campusPhotos as $photo)
            <figure class="showcase-item" data-reveal><a class="showcase-link" href="{{ $photo['src'] }}" data-lightbox data-caption="{{ $photo['caption'] }}" aria-label="Perbesar gambar: {{ $photo['caption'] }}"><img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async"><span class="showcase-zoom" aria-hidden="true">＋</span></a><figcaption><span class="showcase-tag">{{ $photo['tag'] }}</span>{{ $photo['caption'] }}</figcaption></figure>
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="section section-admission" aria-labelledby="admission-heading">
    <div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Alur pendaftaran</p><h2 id="admission-heading">Empat langkah<br><em>menjadi bagian dari kami.</em></h2></div><a class="text-link" href="{{ route('public.page', 'pendaftaran') }}">Persyaratan lengkap <span aria-hidden="true">↗</span></a></div>
        @include('public.partials.admission-steps')
    </div>
</section>
<section class="portal-feature"><div class="container portal-feature-grid"><div class="portal-visual" aria-hidden="true" data-reveal data-tilt><div class="portal-tile tile-back"><span>ILMU</span><i>↗</i></div><div class="portal-tile tile-front"><span class="tile-mark">↗</span><span>Berbagi<br><em>praktik baik.</em></span><small>PORTAL PENGIMBASAN</small></div><div class="portal-visual-star">✳</div></div><div class="portal-feature-copy" data-reveal><p class="eyebrow">Untuk sekolah mitra</p><h2>Pengetahuan tumbuh<br><em>saat dibagikan.</em></h2><p>Akses video, foto, ebook, dan modul pengimbasan yang dibagikan untuk sekolah Anda, dalam satu ruang yang terkelola.</p><div class="portal-types"><span>Video</span><span>Foto</span><span>Ebook</span><span>Modul</span></div><a class="button button-cream" href="{{ route('portal.index') }}">Masuk Portal Pengimbasan <span aria-hidden="true">↗</span></a><p class="portal-note">Akses menggunakan akun yang diberikan oleh pengelola.</p></div></div></section>
<section class="section section-news"><div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Cerita dari Ibnu Aqil</p><h2>Berita & <em>kegiatan.</em></h2></div><a class="text-link" href="{{ route('public.news') }}">Semua berita <span aria-hidden="true">↗</span></a></div>
    @if($news->isNotEmpty())<div class="news-grid">@foreach($news as $article)@include('public.partials.article-card', ['article' => $article])@endforeach</div>
    @else<div class="empty-state empty-inline" data-reveal><span class="empty-mark" aria-hidden="true">↗</span><div><h3>Ruang cerita Ibnu Aqil</h3><p>Berita dan kegiatan dapat dibaca di sini setelah diterbitkan oleh pengelola.</p></div><a class="text-link" href="{{ route('public.page', 'tentang') }}">Kenali lembaga <span aria-hidden="true">→</span></a></div>@endif
</div></section>
<section class="explore-strip"><div class="container"><a href="{{ route('public.page', 'fasilitas') }}" data-reveal><span class="explore-number">01</span><span>Fasilitas & program</span><i aria-hidden="true">↗</i></a><a href="{{ route('public.page', 'prestasi') }}" data-reveal><span class="explore-number">02</span><span>Prestasi</span><i aria-hidden="true">↗</i></a><a href="{{ route('public.gallery') }}" data-reveal><span class="explore-number">03</span><span>Galeri Ibnu Aqil</span><i aria-hidden="true">↗</i></a></div></section>
<section class="contact-banner"><div class="container"><div data-reveal><p class="eyebrow">Langkah berikutnya</p><h2>Mari mengenal<br><em>Ibnu Aqil lebih dekat.</em></h2></div><div data-reveal><p>Kuota setiap unit terbatas. Tanyakan persyaratan, jadwal tes, dan biaya terbaru langsung kepada admin PPDB.</p><div class="hero-actions">@if($ppdbUrl)<a class="button button-glow" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">Chat WhatsApp PPDB <span aria-hidden="true">↗</span></a>@endif<a class="text-link" href="{{ route('public.page', 'pendaftaran') }}">Info pendaftaran <span aria-hidden="true">→</span></a><a class="text-link" href="{{ route('public.page', 'kontak') }}">Hubungi kami <span aria-hidden="true">→</span></a></div></div></div></section>
@endsection
