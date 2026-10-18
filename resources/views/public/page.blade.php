@extends('layouts.public')
@section('title', $page->title.' — Ibnu Aqil Education Centre')
@section('description', $page->summary ?: $page->title.' di Ibnu Aqil Education Centre.')
@section('content')
<section class="page-hero"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">{{ $page->title }}</span></nav><div class="page-hero-grid"><div data-reveal><p class="eyebrow">Ibnu Aqil Education Centre</p><h1>{{ $page->title }}</h1>@if($page->summary)<p class="page-lead">{{ $page->summary }}</p>@endif</div><span class="page-ornament" aria-hidden="true">✳</span></div></div></section>
<section class="section page-section"><div class="container content-layout"><article class="prose" data-reveal>
    @if($page->body)<div class="content-body">{!! nl2br(e($page->body)) !!}</div>@else<p class="content-status">Informasi pada halaman ini belum diterbitkan.</p>@endif
    @if($page->slug === 'kontak')
    <div class="contact-details">
        @if(!empty($settings['contact_address']))<div><h2>Alamat</h2><p>{{ $settings['contact_address'] }}</p></div>@endif
        @if(!empty($settings['contact_phone']))<div><h2>Telepon</h2><p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}">{{ $settings['contact_phone'] }}</a></p></div>@endif
        @if(!empty($settings['contact_email']) && filter_var($settings['contact_email'], FILTER_VALIDATE_EMAIL))<div><h2>Email</h2><p><a href="mailto:{{ $settings['contact_email'] }}">{{ $settings['contact_email'] }}</a></p></div>@endif
    </div>
    @endif
    @if($page->slug === 'pendaftaran')<figure class="brochure-figure"><a href="{{ asset('assets/brosur-2026-2027.jpg') }}" target="_blank" rel="noopener"><img src="{{ asset('assets/brosur-2026-2027.jpg') }}" alt="Brosur resmi Ibnu Aqil tahun ajaran 2026/2027, enam unit pendidikan dan layanan" width="6000" height="1156" loading="lazy"></a><figcaption>Brosur resmi TP 2026/2027. Klik untuk memperbesar. Ketersediaan, jadwal, dan ketentuan pendaftaran dikonfirmasi melalui admin.</figcaption></figure>@endif
    @if($page->slug === 'pendaftaran' && !empty($settings['registration_url']) && preg_match('/^https:\/\//i', $settings['registration_url']))<div class="registration-action"><a href="{{ $settings['registration_url'] }}" class="button" target="_blank" rel="noopener noreferrer">Buka layanan pendaftaran <span aria-hidden="true">↗</span></a><p>Tautan pendaftaran dibuka di tab baru.</p></div>@endif
</article><aside class="page-aside"><p class="eyebrow">Jelajahi Ibnu Aqil</p><nav aria-label="Halaman informasi">@foreach(['tentang' => 'Tentang lembaga', 'fasilitas' => 'Fasilitas & program', 'prestasi' => 'Prestasi', 'pendaftaran' => 'Informasi pendaftaran', 'kontak' => 'Hubungi kami'] as $slug => $label)<a href="{{ route('public.page', $slug) }}" @if($page->slug === $slug) aria-current="page" @endif>{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</nav><div class="aside-note"><span aria-hidden="true">↗</span><h2>Ruang berbagi<br>sekolah mitra.</h2><a class="text-link" href="{{ route('portal.index') }}">Portal Pengimbasan <span aria-hidden="true">→</span></a></div></aside></div></section>
@endsection
