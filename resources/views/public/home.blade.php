@extends('layouts.public')
@section('title', 'Ibnu Aqil Education Center — MI, SMP, SMA, SMK, Pesantren Bogor')
@section('description', 'Yayasan Haji Agus Salim Bogor (YHASB) · Ibnu Aqil Education Center: MI, SMP, SMA, SMK, dan Pondok Pesantren di Laladon, Ciomas. PPDB 2027/2028.')
@php
    use App\Services\{WhatsApp, SchoolProfile, SocialLinks};
    $leaders = SchoolProfile::leaders();
    $ppdbUrl = WhatsApp::link($settings, 'Saya ingin berkonsultasi tentang pendaftaran putra/putri saya di Ibnu Aqil tahun ajaran 2027/2028.');
    $costUrl = WhatsApp::link($settings, WhatsApp::costMessage());
    $visitUrl = WhatsApp::link($settings, 'Saya ingin berkunjung ke sekolah Ibnu Aqil. Kapan waktu yang tepat?');
    $phone = $settings['contact_phone'] ?? '';
    $tel = $phone !== '' ? 'tel:'.preg_replace('/[^0-9+]/', '', $phone) : null;
    $mapsUrl = SocialLinks::maps($settings);
    $heroBg = is_file(public_path('assets/foto/web/masjid-hero.webp')) ? asset('assets/foto/web/masjid-hero.webp') : asset('assets/foto/web/masjid-cahaya-ilahi-wide.jpg');
    $reasons = [
        ['mosque', 'Ibadah harian', 'Dhuha, tadarus, BTQ Tilawati'],
        ['book', '30+ kitab kuning', 'Tradisi pesantren NU'],
        ['bed', 'Asrama pesantren', 'Wajib untuk SMA, pilihan untuk SMP'],
        ['medal', 'Akreditasi A', 'SMP, SMA, dan SMK'],
        ['ball', '25+ ekstrakurikuler', 'Silat, Pramuka, hadroh…'],
        ['star', 'Berdiri sejak 1992', 'Lebih dari 30 tahun'],
    ];
    $steps = [
        ['form', 'Isi formulir', 'Di kantor atau lewat admin WhatsApp'],
        ['check', 'Serahkan dokumen', 'KK, akta, ijazah, pas foto'],
        ['people', 'Tes & wawancara', 'Sesuai jadwal panitia'],
        ['sparkle', 'Daftar ulang', 'Selamat bergabung!'],
    ];
@endphp
@push('head')
<link rel="preload" as="image" href="{{ $heroBg }}">
@endpush
@section('content')
{{-- 1. Sambutan: masjid sebagai latar, pimpinan yayasan di kanan --}}
<section class="ia-hero">
    <img class="ia-hero-bg" src="{{ $heroBg }}" alt="" width="2400" height="1500" fetchpriority="high" decoding="async">
    <div class="ia-hero-inner container">
        <div class="ia-hero-copy">
            <svg class="calligraphy-draw ia-bismillah" viewBox="0 0 690 150" role="img" aria-label="Bismillahirrahmanirrahim"><text x="345" y="112" text-anchor="middle" lang="ar" dir="rtl">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</text></svg>
            <p class="ia-pill">@include('public.partials.icon', ['name' => 'sparkle', 'size' => 16]) PPDB 2027/2028 · Sekolah Ibnu Aqil</p>
            <h1>Assalamu'alaikum,<br><span>selamat datang di Yayasan Haji Agus Salim Bogor</span></h1>
            <p class="ia-hero-brand">YHASB · Ibnu Aqil Education Center</p>
            <p class="ia-hero-lead">MI · SMP · SMA · SMK · Pondok Pesantren<br>Satu kawasan, satu keluarga, sejak 1992.</p>
            <div class="ia-hero-actions">
                @if($ppdbUrl)<a class="ia-btn ia-btn-wa" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) <span><span class="ia-hide-sm">Chat </span>WhatsApp<span class="ia-hide-sm"> PPDB</span></span></a>@endif
                @if($tel)<a class="ia-btn ia-btn-light" href="{{ $tel }}">@include('public.partials.icon', ['name' => 'phone']) Telepon</a>@endif
            </div>
            <p class="ia-hero-hint">@include('public.partials.icon', ['name' => 'clock', 'size' => 18]) Admin melayani Senin–Jumat, 08.00–15.00 WIB</p>
        </div>
        @if($leaders !== [])
        <div class="ia-leaders ia-leaders-{{ count($leaders) }}" aria-label="Pimpinan Yayasan Haji Agus Salim Bogor">
            @foreach($leaders as $leader)
            <figure class="ia-leader">
                <img src="{{ $leader['src'] }}" alt="{{ $leader['name'] }}, {{ $leader['role'] }}" width="{{ $leader['width'] }}" height="{{ $leader['height'] }}" fetchpriority="high" decoding="async">
                <figcaption><strong>{{ $leader['name'] }}</strong><span>{{ $leader['role'] }}</span></figcaption>
            </figure>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- 2. Menu cepat: tombol besar --}}
<section class="ia-quick" aria-labelledby="quick-heading">
    <div class="container">
        <h2 id="quick-heading" class="ia-title">Bapak/Ibu ingin apa hari ini?</h2>
        <div class="ia-quick-grid">
            <a class="ia-tile ia-tile-green" href="{{ route('public.page', 'pendaftaran') }}">@include('public.partials.icon', ['name' => 'form', 'size' => 30])<strong>Cara daftar</strong><span>Syarat & alur</span></a>
            <a class="ia-tile" href="#pilih-sekolah">@include('public.partials.icon', ['name' => 'school', 'size' => 30])<strong>Pilih sekolah</strong><span>MI, SMP, SMA, SMK</span></a>
            @if($costUrl)<a class="ia-tile" href="{{ $costUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'wallet', 'size' => 30])<strong>Tanya rincian biaya</strong><span>Lewat WhatsApp</span></a>@endif
            @if($visitUrl)<a class="ia-tile" href="{{ $visitUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'calendar', 'size' => 30])<strong>Kunjungi sekolah</strong><span>Atur jadwal</span></a>@endif
            @if($mapsUrl)<a class="ia-tile" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'pin', 'size' => 30])<strong>Lokasi</strong><span>Buka Google Maps</span></a>@endif
            <a class="ia-tile" href="{{ route('public.page', 'fasilitas') }}">@include('public.partials.icon', ['name' => 'camera', 'size' => 30])<strong>Fasilitas</strong><span>Lihat foto</span></a>
        </div>
    </div>
</section>

{{-- 3. Kartu sekolah: logo + warna khas tiap unit --}}
<section class="ia-section" id="pilih-sekolah" aria-labelledby="units-heading">
    <div class="container">
        <div class="ia-head"><h2 id="units-heading" class="ia-title">Pilih sekolah</h2></div>
        <span id="unit-ibnu-aqil"></span>
        @include('public.partials.unit-cards', ['only' => $units->pluck('slug')->all()])
    </div>
</section>

{{-- 4. Kenapa Ibnu Aqil: singkat, pakai simbol --}}
<section class="ia-section ia-reasons-section" aria-labelledby="reasons-heading">
    <div class="container">
        <h2 id="reasons-heading" class="ia-title">Kenapa Ibnu Aqil?</h2>
        <ul class="ia-reasons">
            @foreach($reasons as [$icon, $title, $text])
            <li>@include('public.partials.icon', ['name' => $icon, 'size' => 30])<strong>{{ $title }}</strong><span>{{ $text }}</span></li>
            @endforeach
        </ul>
    </div>
</section>


{{-- 5. Alur daftar --}}
<section class="ia-section" aria-labelledby="steps-heading">
    <div class="container">
        <div class="ia-head"><h2 id="steps-heading" class="ia-title">Empat langkah mudah mendaftar</h2><a class="ia-link" href="{{ route('public.page', 'pendaftaran') }}">Syarat lengkap @include('public.partials.icon', ['name' => 'chev', 'size' => 18])</a></div>
        <ol class="ia-steps">
            @foreach($steps as [$icon, $title, $text])
            <li><span class="ia-step-num">{{ $loop->iteration }}</span>@include('public.partials.icon', ['name' => $icon, 'size' => 28])<strong>{{ $title }}</strong><span>{{ $text }}</span></li>
            @endforeach
        </ol>
    </div>
</section>

{{-- 6. Suasana: foto bergerak --}}
<section class="ia-section ia-life" aria-labelledby="life-heading">
    <div class="container"><div class="ia-head"><h2 id="life-heading" class="ia-title">Suasana sehari-hari</h2><a class="ia-link" href="{{ route('public.page', 'fasilitas') }}">Lihat fasilitas @include('public.partials.icon', ['name' => 'chev', 'size' => 18])</a></div></div>
    @include('public.partials.photo-marquee')
</section>

@include('public.partials.wa-faq')


{{-- 7. Kontak & lokasi --}}
<section class="ia-contact" aria-labelledby="contact-heading">
    <div class="container ia-contact-inner">
        <div>
            <h2 id="contact-heading">Masih ragu? Tanya saja.</h2>
            <p>Admin kami siap menjelaskan dengan sabar, termasuk soal biaya dan program mondok.</p>
            <ul class="ia-contact-list">
                @if(!empty($settings['address']))<li>@include('public.partials.icon', ['name' => 'pin']) <span>{{ strtok($settings['address'], "\n") }}</span></li>@endif
                <li>@include('public.partials.icon', ['name' => 'clock']) <span>Senin–Jumat, 08.00–15.00 WIB</span></li>
                @if($phone !== '')<li>@include('public.partials.icon', ['name' => 'phone']) <span>{{ $phone }}</span></li>@endif
            </ul>
        </div>
        <div class="ia-contact-actions">
            @if($ppdbUrl)<a class="ia-btn ia-btn-wa ia-btn-block" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) Chat WhatsApp PPDB</a>@endif
            @if($tel)<a class="ia-btn ia-btn-light ia-btn-block" href="{{ $tel }}">@include('public.partials.icon', ['name' => 'phone']) Telepon kantor</a>@endif
            @if($mapsUrl)<a class="ia-btn ia-btn-light ia-btn-block" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'pin']) Petunjuk arah</a>@endif
        </div>
    </div>
</section>
@endsection
