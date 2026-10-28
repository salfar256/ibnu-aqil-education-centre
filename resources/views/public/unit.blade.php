@extends('layouts.public')
@section('title', $unit->title.' — Ibnu Aqil Education Center')
@section('description', $unit->summary ?: 'Kenali '.$unit->title.' melalui profil unit di Ibnu Aqil Education Center.')
@php
    use App\Services\{Units, WhatsApp};
    $info = Units::get($unit->slug);
    $color = $info['color'] ?? 'pesantren';
    $ppdbUrl = WhatsApp::link($settings, WhatsApp::unitMessage($unit->slug));
    $costUrl = WhatsApp::link($settings, WhatsApp::costMessage($unit->slug));
    $mondokUrl = WhatsApp::link($settings, 'Saya tertarik putra/putri saya sambil mondok di Pondok Pesantren Ibnu Aqil. Bagaimana ketentuannya?');
    $ekskul = $info ? Units::ekskul($info['ekskul'], $info['ekskul_style']) : [];
    $brochure = $info['brochure_files'] ?? null;
@endphp
@section('content')
<div class="ia-unit-page ia-c-{{ $color }}">
{{-- 1. Pembuka berwarna: logo, nama, kepala sekolah --}}
<section class="ia-unit-hero">
    <div class="container ia-unit-hero-inner">
        <div class="ia-unit-hero-copy">
            <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <a href="{{ route('public.home') }}#pilih-sekolah">Sekolah</a></nav>
            @if($info && $info['logo'])<span class="ia-unit-hero-logo"><img src="{{ $info['logo'] }}" alt="Logo {{ $unit->title }}" width="120" height="120" decoding="async"></span>@endif
            <p class="ia-ppdb-tag">@include('public.partials.icon', ['name' => 'sparkle', 'size' => 16]) PPDB {{ Units::PPDB_YEAR }}</p>
            <h1>{{ $unit->title }}</h1>
            @if($info)<p class="ia-unit-hero-line">{{ $info['line'] }}</p>@endif
            <ul class="ia-unit-facts">
                @if($info)<li>@include('public.partials.icon', ['name' => 'user', 'size' => 18]) {{ $info['who'] }}</li>
                <li>@include('public.partials.icon', ['name' => 'clock', 'size' => 18]) {{ $info['schedule'] }}</li>
                @if($info['boarding'] && $unit->slug !== 'pesantren')<li class="ia-fact-mondok"><a href="{{ route('public.unit', 'pesantren') }}">@include('public.partials.icon', ['name' => 'bed', 'size' => 18]) {{ $info['boarding'] }}</a></li>@endif
                @endif
            </ul>
            <div class="ia-hero-actions">
                @if($ppdbUrl)<a class="ia-btn ia-btn-wa" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) <span>Tanya {{ WhatsApp::UNITS[$unit->slug][0] ?? '' }} via WhatsApp</span></a>@endif
                @if($brochure && $brochure['pdf'])<a class="ia-btn ia-btn-light" href="{{ $brochure['pdf'] }}" download>@include('public.partials.icon', ['name' => 'download']) Unduh brosur</a>@endif
            </div>
        </div>
        @if($info && $info['leader_photo'])
        <figure class="ia-head-person">
            <img src="{{ $info['leader_photo']['src'] }}" alt="{{ $info['leader'][0] }}, {{ $info['leader'][1] }} {{ $unit->title }}" width="{{ $info['leader_photo']['width'] }}" height="{{ $info['leader_photo']['height'] }}" fetchpriority="high" decoding="async">
            <figcaption><strong>{{ $info['leader'][0] }}</strong><span>{{ $info['leader'][1] }}</span></figcaption>
        </figure>
        @endif
    </div>
</section>

@if($info)
{{-- 2. Visi singkat --}}
<section class="ia-vision">
    <div class="container ia-vision-inner">
        <div>
            <p class="ia-vision-label">@include('public.partials.icon', ['name' => 'compass', 'size' => 20]) Visi kami</p>
            <blockquote>“{{ $info['vision'] }}”</blockquote>
            @if(!empty($info['motto']))<p class="ia-motto">{{ $info['motto'] }}</p>@endif
        </div>
    </div>
</section>

@if(!empty($info['majors']))
{{-- SMK: tiga jurusan --}}
<section class="ia-section ia-majors-section">
    <div class="container">
        <h2 class="ia-title">3 jurusan pilihan</h2>
        <ul class="ia-majors">
            @foreach($info['majors'] as [$icon, $label, $text])
            <li><span class="ia-major-icon">@include('public.partials.icon', ['name' => $icon, 'size' => 34])</span><span class="ia-major-num">{{ $loop->iteration }}</span><strong>{{ $label }}</strong><span>{{ $text }}</span></li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- 3. Program unggulan dari brosur --}}
<section class="ia-section">
    <div class="container">
        <h2 class="ia-title">Program unggulan</h2>
        <ul class="ia-programs">
            @foreach($info['programs'] as [$icon, $label])
            <li><span class="ia-program-icon">@include('public.partials.icon', ['name' => $icon, 'size' => 26])</span><strong>{{ $label }}</strong></li>
            @endforeach
        </ul>
        @if(!empty($info['note']))<p class="ia-note">@include('public.partials.icon', ['name' => 'help', 'size' => 18]) {{ $info['note'] }}</p>@endif
    </div>
</section>

{{-- 4. Foto siswa --}}
@if($info['student_photos'] !== [])
<section class="ia-students">
    <div class="container ia-students-inner">
        <div class="ia-students-copy">
            <h2 class="ia-title">Siswa {{ $info['short'] }} Ibnu Aqil</h2>
            <p>Semangat belajar, rapi, dan percaya diri.</p>
            @if($info['boarding'] && $unit->slug !== 'pesantren' && $mondokUrl)
            <a class="ia-mondok-cta" href="{{ route('public.unit', 'pesantren') }}">@include('public.partials.icon', ['name' => 'bed', 'size' => 22]) <span><strong>{{ $info['boarding'] }}</strong><small>Siswa {{ $info['short'] }} bisa tinggal di asrama pesantren. Pilihan, tidak wajib.</small></span></a>
            @endif
        </div>
        <div class="ia-students-row">
            @foreach($info['student_photos'] as $photo)
            <img src="{{ $photo['src'] }}" alt="Siswa {{ $unit->title }}" width="{{ $photo['width'] }}" height="{{ $photo['height'] }}" loading="lazy" decoding="async">
            @endforeach
        </div>
    </div>
</section>
@endif

@if($ekskul !== [])
{{-- 5. Ekstrakurikuler bergambar stiker --}}
<section class="ia-section ia-ekskul-section">
    <div class="container">
        <h2 class="ia-title">Ekstrakurikuler</h2>
        <ul class="ia-ekskul ia-ekskul-{{ $info['ekskul_style'] }}">
            @foreach($ekskul as $e)
            <li>@if($e['src'])<img src="{{ $e['src'] }}" alt="" width="96" height="96" loading="lazy" decoding="async">@else @include('public.partials.icon', ['name' => 'star', 'size' => 40]) @endif<span>{{ $e['label'] }}</span></li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- 6. Brosur --}}
@if($brochure && $brochure['pages'] !== [])
<section class="ia-section ia-brochure-section" id="brosur">
    <div class="container ia-brochure-inner">
        <div>
            <h2 class="ia-title">Brosur PPDB {{ Units::PPDB_YEAR }}</h2>
            <p>Simpan di HP, lalu kirim ke keluarga lewat WhatsApp.</p>
            <div class="ia-brochure-actions">
                @if($brochure['pdf'])<a class="ia-btn ia-btn-small" href="{{ $brochure['pdf'] }}" download>@include('public.partials.icon', ['name' => 'download', 'size' => 20]) Unduh PDF ({{ number_format($brochure['pdf_kb'] / 1024, 1, ',', '') }} MB)</a>@endif
                @if($brochure['drive'])<a class="ia-link" href="{{ $brochure['drive'] }}" target="_blank" rel="noopener noreferrer">Buka di Google Drive @include('public.partials.icon', ['name' => 'chev', 'size' => 18])</a>@endif
            </div>
        </div>
        <div class="ia-brochure-pages">
            @foreach($brochure['pages'] as $page)
            <a href="{{ $page['hd'] }}" data-lightbox data-caption="Brosur {{ $unit->title }} halaman {{ $loop->iteration }}" aria-label="Perbesar brosur halaman {{ $loop->iteration }}"><img src="{{ $page['src'] }}" alt="Brosur {{ $unit->title }} halaman {{ $loop->iteration }}" width="{{ $page['width'] }}" height="{{ $page['height'] }}" loading="lazy" decoding="async"></a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endif

@include('public.partials.wa-faq', ['faqUnit' => $unit->slug])

{{-- 7. Ajakan + pindah ke unit lain --}}
<section class="ia-contact">
    <div class="container ia-contact-inner">
        <div>
            <h2>Siap bergabung dengan {{ $info['short'] ?? $unit->title }}?</h2>
            <p>Tanyakan pendaftaran, jadwal tes, dan biaya langsung ke admin PPDB.</p>
        </div>
        <div class="ia-contact-actions">
            @if($ppdbUrl)<a class="ia-btn ia-btn-wa ia-btn-block" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) Chat WhatsApp PPDB</a>@endif
            @if($costUrl)<a class="ia-btn ia-btn-light ia-btn-block" href="{{ $costUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'wallet']) Tanya rincian biaya</a>@endif
            <a class="ia-btn ia-btn-light ia-btn-block" href="{{ route('public.page', 'pendaftaran') }}">@include('public.partials.icon', ['name' => 'form']) Cara mendaftar</a>
        </div>
    </div>
</section>
<section class="ia-section ia-other-units">
    <div class="container">
        <h2 class="ia-title">Sekolah lainnya</h2>
        <nav class="ia-unit-pills" aria-label="Unit Ibnu Aqil lainnya">
            @foreach(Units::all() as $u)
            @continue($u['slug'] === $unit->slug)
            <a class="ia-c-{{ $u['color'] }}" href="{{ route('public.unit', $u['slug']) }}">@if($u['logo'])<img src="{{ $u['logo'] }}" alt="" width="40" height="40" loading="lazy">@endif {{ $u['name'] }}</a>
            @endforeach
        </nav>
    </div>
</section>
</div>
@endsection
