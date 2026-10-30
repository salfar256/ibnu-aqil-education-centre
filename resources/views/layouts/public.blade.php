<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#103e35">
    <title>@yield('title', 'Ibnu Aqil Education Center')</title>
    <meta name="description" content="@yield('description', 'MI, SMP, SMA Boarding School, SMK, dan Pondok Pesantren Ibnu Aqil di Laladon, Ciomas, Bogor. Tanyakan pendaftaran 2027/2028 langsung kepada admin PPDB melalui WhatsApp.')">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'Ibnu Aqil Education Center')">
    <meta property="og:description" content="@yield('description', 'MI, SMP, SMA Boarding School, SMK, dan Pondok Pesantren Ibnu Aqil di Laladon, Ciomas, Bogor. Tanyakan pendaftaran 2027/2028 langsung kepada admin PPDB melalui WhatsApp.')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="Ibnu Aqil Education Center">
    <meta property="og:image" content="{{ asset('assets/brosur-2026-2027.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('assets/icons/favicon-48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/icons/favicon-96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/icons/favicon-192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/ramah.css') }}">
    <link rel="manifest" href="{{ asset('assets/manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icons/icon-192.png') }}">
    @php($orgSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => 'Ibnu Aqil Education Center',
        'alternateName' => ['Sekolah Ibnu Aqil', 'Yayasan Haji Agus Salim Bogor'],
        'url' => route('public.home'),
        'logo' => asset('assets/icons/favicon-192.png'),
        'foundingDate' => '1992',
        'sameAs' => array_values(array_column(\App\Services\SocialLinks::social($settings ?? []), 'url')),
    ]))
    <script type="application/ld+json">{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Ibnu Aqil">
    <script src="{{ asset('assets/site.js') }}" defer></script>
    @stack('head')
</head>
@php($waSettings = $settings ?? [])
<body>
    <div class="scroll-progress" aria-hidden="true"><span></span></div>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <div class="topline"><div class="container"><span>Ibnu Aqil Education Center</span>@if($toplineWa = \App\Services\WhatsApp::link($waSettings, 'Saya ingin bertanya tentang pendaftaran tahun ajaran 2027/2028.'))<a href="{{ $toplineWa }}" target="_blank" rel="noopener noreferrer">Informasi PPDB 2027/2028 — tanya admin via WhatsApp <span aria-hidden="true">↗</span></a>@endif</div></div>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('public.home') }}" aria-label="Ibnu Aqil Education Center, beranda">
                <img class="official-logo" src="{{ asset(\App\Services\PublicPhotos::logo()) }}" alt="Logo Yayasan Haji Agus Salim Bogor" width="48" height="49">
                <span class="brand-name">Ibnu Aqil<small>EDUCATION CENTER</small></span>
            </a>
            <button type="button" class="menu-toggle" aria-controls="primary-nav" aria-expanded="false"><span class="menu-icon" aria-hidden="true"><i></i><i></i><i></i></span><span class="sr-only">Buka menu navigasi</span></button>
            <nav class="primary-nav" id="primary-nav" aria-label="Navigasi utama">
                <a href="{{ route('public.page', 'tentang') }}" @if(request()->is('tentang')) aria-current="page" @endif>Tentang</a>
                <details class="nav-dropdown">
                    <summary>Sekolah <svg viewBox="0 0 16 16" width="12" height="12" aria-hidden="true"><path d="m4 6 4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg></summary>
                    <div class="nav-dropdown-panel">
                        @foreach(['mi' => 'MI Ibnu Aqil', 'smp' => 'SMP Ibnu Aqil', 'sma' => 'SMA Ibnu Aqil', 'smk' => 'SMK Ibnu Aqil', 'pesantren' => 'Pondok Pesantren (mondok opsional)'] as $slug => $label)
                        <a href="{{ route('public.unit', $slug) }}">{{ $label }} <span aria-hidden="true">↗</span></a>
                        @endforeach
                    </div>
                </details>
                <a href="{{ route('public.page', 'fasilitas') }}" @if(request()->is('fasilitas')) aria-current="page" @endif>Fasilitas</a>
                <a href="{{ route('public.page', 'kontak') }}" class="nav-portal" @if(request()->is('kontak')) aria-current="page" @endif>Kontak</a>
                <a href="{{ route('public.page', 'pendaftaran') }}" class="button button-small">Info pendaftaran <span aria-hidden="true">↗</span></a>
            </nav>
        </div>
    </header>
    <main id="main-content" tabindex="-1">@yield('content')</main>
    <footer class="site-footer">
        <div class="container footer-main">
            <div class="footer-brand-column">
                <a class="brand brand-light" href="{{ route('public.home') }}"><img class="official-logo" src="{{ asset(\App\Services\PublicPhotos::logo()) }}" alt="Logo Yayasan Haji Agus Salim Bogor" width="48" height="49"><span class="brand-name">Ibnu Aqil<small>EDUCATION CENTER</small></span></a>
                <p>Yayasan Haji Agus Salim Bogor (YHASB)<br>MI · SMP · SMA · SMK · Pondok Pesantren</p>
                @if($footerWa = \App\Services\WhatsApp::link($waSettings, 'Saya ingin bertanya tentang pendaftaran di Ibnu Aqil.'))<a class="footer-portal" href="{{ $footerWa }}" target="_blank" rel="noopener noreferrer">Tanya admin PPDB via WhatsApp <span aria-hidden="true">↗</span></a>@endif
            </div>
            <div><h2>Jelajahi</h2><a href="{{ route('public.page', 'tentang') }}">Tentang Ibnu Aqil</a><a href="{{ route('public.page', 'fasilitas') }}">Fasilitas & kegiatan</a><a href="{{ route('public.page', 'pendaftaran') }}">Cara mendaftar</a><a href="{{ route('public.page', 'kontak') }}">Kontak</a></div>
            <div><h2>Sekolah</h2>@foreach(['mi' => 'MI', 'smp' => 'SMP', 'sma' => 'SMA', 'smk' => 'SMK', 'pesantren' => 'Pondok Pesantren'] as $slug => $label)<a href="{{ route('public.unit', $slug) }}">{{ $label }} Ibnu Aqil</a>@endforeach</div>
            <div class="footer-contact"><h2>Mari terhubung</h2>
                <ul class="ia-foot-list">
                    @if(!empty($settings['contact_address']))<li>@include('public.partials.icon', ['name' => 'pin', 'size' => 18])<span>{{ strtok($settings['contact_address'], "\n") }}</span></li>@endif
                    @if(!empty($settings['contact_phone']))<li>@include('public.partials.icon', ['name' => 'phone', 'size' => 18])<a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}">{{ $settings['contact_phone'] }}</a></li>@endif
                    @if($footerWa)<li>@include('public.partials.icon', ['name' => 'whatsapp', 'size' => 18])<a href="{{ $footerWa }}" target="_blank" rel="noopener noreferrer">WhatsApp PPDB</a></li>@endif
                    @if($footerMapsUrl = \App\Services\SocialLinks::maps($settings ?? []))<li>@include('public.partials.icon', ['name' => 'compass', 'size' => 18])<a href="{{ $footerMapsUrl }}" target="_blank" rel="noopener noreferrer">Petunjuk arah (Google Maps)</a></li>@endif
                    <li>@include('public.partials.icon', ['name' => 'clock', 'size' => 18])<span>Senin–Jumat · 08.00–15.00 WIB</span></li>
                </ul>
                <p class="ia-foot-social-title">Ikuti kami di media sosial</p>
                @include('public.partials.social-links')
            </div>
        </div>
        <div class="container footer-bottom"><span>© {{ date('Y') }} Ibnu Aqil Education Center</span><a href="{{ route('login') }}">Akses pengelola <span aria-hidden="true">↗</span></a></div>
    </footer>
    @php($waMenu = \App\Services\WhatsApp::menu($waSettings))
    @if($waMenu !== [])
    <details class="wa-float">
        <summary class="wa-float-toggle" aria-label="Chat WhatsApp PPDB Ibnu Aqil, pilih topik"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.8a8 8 0 0 1-11.9 7L4 20l1.2-4A8 8 0 1 1 20 11.8Z" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M8.5 8.3c-.7 1.9 1.8 5.8 4.8 6.9 1.2.5 2.1-.8 2.1-1.1l-2.1-1-.7.7c-1.3-.7-2.3-1.6-2.8-2.8l.6-.7-1-2Z" fill="currentColor"/></svg><span>WhatsApp PPDB</span></summary>
        <div class="wa-menu" role="group" aria-label="Topik pertanyaan">
            <p class="wa-menu-head"><strong>Tanya admin PPDB</strong><span>Pilih topik, pesan langsung tersusun di WhatsApp.</span></p>
            @foreach($waMenu as $waItem)<a href="{{ $waItem['href'] }}" target="_blank" rel="noopener noreferrer">{{ $waItem['label'] }} <span aria-hidden="true">↗</span></a>@endforeach
        </div>
    </details>
    @endif
    @php($navWa = \App\Services\WhatsApp::link($waSettings, 'Saya ingin bertanya tentang pendaftaran di Ibnu Aqil.'))
    @php($navMaps = \App\Services\SocialLinks::maps($waSettings))
    <nav class="ia-bottom-nav" aria-label="Menu cepat">
        <a href="{{ route('public.home') }}" @if(request()->routeIs('public.home')) aria-current="page" @endif>@include('public.partials.icon', ['name' => 'home'])<span>Beranda</span></a>
        <a href="{{ route('public.home') }}#pilih-sekolah">@include('public.partials.icon', ['name' => 'school'])<span>Sekolah</span></a>
        @if($navWa)<a class="ia-bottom-wa" href="{{ $navWa }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp'])<span>WhatsApp</span></a>@endif
        <a href="{{ route('public.page', 'pendaftaran') }}" @if(request()->is('pendaftaran')) aria-current="page" @endif>@include('public.partials.icon', ['name' => 'form'])<span>Daftar</span></a>
        @if($navMaps)<a href="{{ $navMaps }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'pin'])<span>Lokasi</span></a>@else<a href="{{ route('public.page', 'kontak') }}">@include('public.partials.icon', ['name' => 'phone'])<span>Kontak</span></a>@endif
    </nav>
    <a class="back-to-top" href="#main-content" aria-label="Kembali ke atas"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 19V5m-6 6 6-6 6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
    <dialog class="lightbox" aria-label="Pratinjau gambar"><form method="dialog"><button type="submit" class="lightbox-close" aria-label="Tutup pratinjau">×</button></form><figure class="lightbox-figure"></figure><p class="lightbox-caption"></p></dialog>
</body>
</html>
