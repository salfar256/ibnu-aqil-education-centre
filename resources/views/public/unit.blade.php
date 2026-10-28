@extends('layouts.public')
@section('title', $unit->title.' — Ibnu Aqil Education Centre')
@section('description', $unit->summary ?: 'Kenali '.$unit->title.' melalui profil unit di Ibnu Aqil Education Centre.')
@php
    use App\Services\{Units, WhatsApp};
    $info = Units::get($unit->slug);
    $color = $info['color'] ?? 'pesantren';
    $ppdbUrl = WhatsApp::link($settings, WhatsApp::unitMessage($unit->slug));
    $costUrl = WhatsApp::link($settings, WhatsApp::costMessage($unit->slug));
    $ekskul = $info ? Units::ekskul($info['ekskul']) : [];
@endphp
@section('content')
<div class="ia-unit-page ia-c-{{ $color }}">
{{-- 1. Pembuka berwarna: logo, nama, kepala sekolah --}}
<section class="ia-unit-hero">
    <div class="container ia-unit-hero-inner">
        <div class="ia-unit-hero-copy">
            <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <a href="{{ route('public.home') }}#pilih-sekolah">Sekolah</a></nav>
            @if($info && $info['logo'])<span class="ia-unit-hero-logo"><img src="{{ $info['logo'] }}" alt="Logo {{ $unit->title }}" width="120" height="120" decoding="async"></span>@endif
            <h1>{{ $unit->title }}</h1>
            @if($info)<p class="ia-unit-hero-line">{{ $info['line'] }}</p>@endif
            <ul class="ia-unit-facts">
                @if($info)<li>@include('public.partials.icon', ['name' => 'user', 'size' => 18]) {{ $info['who'] }}</li>
                <li>@include('public.partials.icon', ['name' => 'clock', 'size' => 18]) {{ $info['schedule'] }}</li>
                @if($info['boarding'])<li>@include('public.partials.icon', ['name' => 'bed', 'size' => 18]) {{ $info['boarding'] }}</li>@endif
                @endif
            </ul>
            <div class="ia-hero-actions">
                @if($ppdbUrl)<a class="ia-btn ia-btn-wa" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">@include('public.partials.icon', ['name' => 'whatsapp']) <span>Tanya {{ WhatsApp::UNITS[$unit->slug][0] ?? '' }} via WhatsApp</span></a>@endif
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
    <div class="container">
        <p class="ia-vision-label">@include('public.partials.icon', ['name' => 'compass', 'size' => 20]) Visi kami</p>
        <blockquote>“{{ $info['vision'] }}”</blockquote>
    </div>
</section>

{{-- 3. Keunggulan: 4 kartu ikon --}}
<section class="ia-section">
    <div class="container">
        <h2 class="ia-title">Yang Ananda dapatkan</h2>
        <ul class="ia-reasons ia-reasons-light">
            @foreach($info['highlights'] as [$icon, $label])
            <li>@include('public.partials.icon', ['name' => $icon, 'size' => 30])<strong>{{ $label }}</strong></li>
            @endforeach
        </ul>
    </div>
</section>

@if($ekskul !== [])
{{-- 4. Ekstrakurikuler bergambar stiker --}}
<section class="ia-section ia-ekskul-section">
    <div class="container">
        <h2 class="ia-title">Ekstrakurikuler</h2>
        <ul class="ia-ekskul">
            @foreach($ekskul as $e)
            <li>@if($e['src'])<img src="{{ $e['src'] }}" alt="" width="96" height="96" loading="lazy" decoding="async">@else @include('public.partials.icon', ['name' => 'star', 'size' => 40]) @endif<span>{{ $e['label'] }}</span></li>
            @endforeach
        </ul>
    </div>
</section>
@endif
@endif

@include('public.partials.wa-faq', ['faqUnit' => $unit->slug])

{{-- 5. Ajakan + pindah ke unit lain --}}
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
