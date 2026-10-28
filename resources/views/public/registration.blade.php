@extends('layouts.public')
@section('title', 'Info Pendaftaran PPDB 2027/2028 — Ibnu Aqil Education Center')
@section('description', 'Cara mendaftar di Sekolah Ibnu Aqil tahun ajaran 2027/2028: alur, syarat dokumen, dan brosur MI, SMP, SMA yang bisa diunduh.')
@php
    use App\Services\{Units, WhatsApp};
    $icon = fn (string $name) => is_file(public_path('assets/icons/daftar/'.$name.'.webp')) ? asset('assets/icons/daftar/'.$name.'.webp') : null;
    $ppdbUrl = WhatsApp::link($settings, 'Saya ingin mendaftarkan putra/putri saya di Ibnu Aqil tahun ajaran 2027/2028. Mohon informasinya.');
    $students = array_values(array_filter(['mi-1', 'smp-1', 'sma-2'], fn ($k) => is_file(public_path('assets/foto/siswa/'.$k.'.webp'))));
    $steps = [
        ['cari-info', 'Cari info', 'Baca brosur atau tanya admin lewat WhatsApp.'],
        ['isi-formulir', 'Isi formulir', 'Ambil nomor antrean, lalu isi formulir pendaftaran.'],
        ['unggah-berkas', 'Serahkan berkas', 'Lampirkan dokumen persyaratan.'],
        ['verifikasi', 'Verifikasi', 'Panitia memeriksa kelengkapan dokumen.'],
        ['wawancara', 'Tes & wawancara', 'Sesuai jadwal dari panitia PPDB.'],
        ['daftar-ulang', 'Daftar ulang', 'Setelah diterima, selesaikan administrasi.'],
    ];
    $docsMi = ['Formulir pendaftaran', 'Fotokopi KTP orang tua/wali (2 lembar)', 'Fotokopi Kartu Keluarga (2 lembar)', 'Fotokopi akta lahir (2 lembar)', 'Fotokopi STTB RA/TK/PAUD (2 lembar)', 'Pernah sekolah formal minimal 1 tahun'];
    $docsUpper = ['Formulir pendaftaran', 'Fotokopi ijazah legalisir (2 lembar)', 'Pas foto 3×4 (3 lembar)', 'Fotokopi KTP orang tua/wali (2 lembar)', 'Fotokopi Kartu Keluarga (2 lembar)', 'Fotokopi akta lahir (2 lembar)', 'Fotokopi kartu NISN (jika ada)', 'Surat keterangan berkelakuan baik dari sekolah asal'];
@endphp
@section('content')
{{-- Pembuka: siswa bersemangat --}}
<section class="ia-reg-hero">
    <div class="container ia-reg-hero-inner">
        <div class="ia-reg-copy">
            <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <span aria-current="page">Info pendaftaran</span></nav>
            <p class="ia-ppdb-tag">@include('public.partials.icon', ['name' => 'sparkle', 'size' => 16]) PPDB {{ Units::PPDB_YEAR }} · Sekolah Ibnu Aqil</p>
            <h1>Ayo daftar,<br><span>kuota terbatas!</span></h1>
            <ul class="ia-unit-facts">
                <li>@include('public.partials.icon', ['name' => 'calendar', 'size' => 18]) Dibuka mulai 14 November 2026</li>
                <li>@include('public.partials.icon', ['name' => 'clock', 'size' => 18]) Setiap hari kerja · 08.00–15.00 WIB</li>
            </ul>
            <div class="ia-hero-actions">
                @if($ppdbUrl)<a class="ia-btn ia-btn-wa" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) <span>Daftar via WhatsApp</span></a>@endif
                <a class="ia-btn ia-btn-light" href="#brosur">@include('public.partials.icon', ['name' => 'download']) Unduh brosur</a>
            </div>
        </div>
        @if($students !== [])
        <div class="ia-reg-students">
            @foreach($students as $k)
            @php($sz = getimagesize(public_path('assets/foto/siswa/'.$k.'.webp')))
            <img src="{{ asset('assets/foto/siswa/'.$k.'.webp') }}" alt="Siswa Ibnu Aqil yang bersemangat" width="{{ $sz[0] }}" height="{{ $sz[1] }}" fetchpriority="high" decoding="async">
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Alur pendaftaran --}}
<section class="ia-section">
    <div class="container">
        <h2 class="ia-title">Alur pendaftaran</h2>
        <ol class="ia-reg-steps">
            @foreach($steps as [$ic, $title, $text])
            <li>
                <span class="ia-step-num">{{ $loop->iteration }}</span>
                @if($icon($ic))<img src="{{ $icon($ic) }}" alt="" width="80" height="80" loading="lazy">@endif
                <strong>{{ $title }}</strong><span>{{ $text }}</span>
            </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- Syarat dokumen --}}
<section class="ia-section ia-ekskul-section" id="syarat">
    <div class="container">
        <h2 class="ia-title ia-title-icon">@if($icon('dokumen'))<img class="ia-title-img" src="{{ $icon('dokumen') }}" alt="" width="48" height="48" loading="lazy">@endif Syarat dokumen</h2>
        <div class="ia-docs">
            <div class="ia-docs-card ia-c-mi"><h3>MI Ibnu Aqil</h3><ul>@foreach($docsMi as $d)<li>@include('public.partials.icon', ['name' => 'check', 'size' => 20]) {{ $d }}</li>@endforeach</ul></div>
            <div class="ia-docs-card ia-c-smp"><h3>SMP · SMA · SMK</h3><ul>@foreach($docsUpper as $d)<li>@include('public.partials.icon', ['name' => 'check', 'size' => 20]) {{ $d }}</li>@endforeach</ul></div>
        </div>
        <p class="ia-note">@include('public.partials.icon', ['name' => 'help', 'size' => 18]) SMA tidak menerima siswa pindahan. Rincian biaya dijelaskan langsung oleh admin PPDB.</p>
    </div>
</section>

{{-- Unduh brosur per sekolah --}}
<section class="ia-section" id="brosur">
    <div class="container">
        <h2 class="ia-title">Unduh brosur</h2>
        <div class="ia-brochure-cards">
            @foreach(Units::all() as $u)
            @continue($u['slug'] === 'pesantren')
            <article class="ia-brochure-card ia-c-{{ $u['color'] }}">
                <div class="ia-brochure-thumb">
                    @if($u['brochure_files'] && $u['brochure_files']['pages'] !== [])
                    <a href="{{ $u['brochure_files']['pages'][0]['hd'] }}" data-lightbox data-caption="Brosur {{ $u['name'] }}" aria-label="Perbesar brosur {{ $u['name'] }}"><img src="{{ $u['brochure_files']['pages'][0]['src'] }}" alt="Brosur {{ $u['name'] }}" width="{{ $u['brochure_files']['pages'][0]['width'] }}" height="{{ $u['brochure_files']['pages'][0]['height'] }}" loading="lazy" decoding="async"></a>
                    @else
                    <span class="ia-brochure-empty">@if($u['logo'])<img src="{{ $u['logo'] }}" alt="" width="90" height="90" loading="lazy">@endif</span>
                    @endif
                </div>
                <div class="ia-brochure-body">
                    <strong>{{ $u['name'] }}</strong>
                    @if($u['brochure_files'] && $u['brochure_files']['pdf'])
                    <a class="ia-btn ia-btn-small" href="{{ $u['brochure_files']['pdf'] }}" download>@include('public.partials.icon', ['name' => 'download', 'size' => 20]) Unduh PDF</a>
                    @if($u['brochure_files']['drive'])<a class="ia-link" href="{{ $u['brochure_files']['drive'] }}" target="_blank" rel="noopener noreferrer">Google Drive @include('public.partials.icon', ['name' => 'chev', 'size' => 16])</a>@endif
                    @elseif($w = WhatsApp::link($settings, 'Saya ingin meminta brosur '.$u['name'].' tahun ajaran 2027/2028.'))
                    <a class="ia-btn ia-btn-small" href="{{ $w }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp', 'size' => 20]) Minta brosur</a>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

@if($page->body)
<section class="ia-section ia-fac-list">
    <div class="container"><details class="faq-item"><summary><span>Keterangan lengkap</span><i aria-hidden="true">+</i></summary><div class="faq-answer"><div class="content-body">{!! nl2br(e($page->body)) !!}</div></div></details></div>
</section>
@endif

@include('public.partials.wa-faq')
@endsection
