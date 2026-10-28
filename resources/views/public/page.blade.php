@extends('layouts.public')
@section('title', $page->title.' — Ibnu Aqil Education Center')
@section('description', $page->summary ?: $page->title.' di Ibnu Aqil Education Center.')
@section('content')
<section class="page-hero"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">{{ $page->title }}</span></nav><div class="page-hero-grid"><div data-reveal><p class="eyebrow">Ibnu Aqil Education Center</p><h1>{{ $page->title }}</h1>@if($page->summary)<p class="page-lead">{{ $page->summary }}</p>@endif</div><span class="page-ornament" aria-hidden="true">✳</span></div></div></section>
<section class="section page-section"><div class="container content-layout"><article class="prose" data-reveal>
    @if($page->body)<div class="content-body">{!! nl2br(e($page->body)) !!}</div>@else<p class="content-status">Informasi pada halaman ini belum diterbitkan.</p>@endif
    @if($page->slug === 'kontak')
    <div class="contact-details">
        @if(!empty($settings['contact_address']))<div><h2>Alamat</h2><p>{!! nl2br(e($settings['contact_address'])) !!}</p></div>@endif
        @if(!empty($settings['contact_phone']))<div><h2>Telepon</h2><p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}">{{ $settings['contact_phone'] }}</a></p></div>@endif
        @if(preg_match('~^https://wa\.me/([0-9]{9,15})$~', (string) ($settings['whatsapp_url'] ?? ''), $ppdbWhatsapp))<div><h2>WhatsApp PPDB</h2><p><a href="{{ $settings['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer">{{ str_starts_with($ppdbWhatsapp[1], '62') ? implode('-', str_split('0'.substr($ppdbWhatsapp[1], 2), 4)) : '+'.$ppdbWhatsapp[1] }}</a></p></div>@endif
        @if($contactMapsUrl = \App\Services\SocialLinks::maps($settings))<div><h2>Lokasi</h2><p><a href="{{ $contactMapsUrl }}" target="_blank" rel="noopener noreferrer">Buka di Google Maps</a></p></div>@endif
        @if(\App\Services\SocialLinks::social($settings) !== [])<div><h2>Media sosial</h2>@include('public.partials.social-links')</div>@endif
        @if(!empty($settings['contact_email']) && filter_var($settings['contact_email'], FILTER_VALIDATE_EMAIL))<div><h2>Email</h2><p><a href="mailto:{{ $settings['contact_email'] }}">{{ $settings['contact_email'] }}</a></p></div>@endif
    </div>
    @endif
    @if($page->slug === 'pendaftaran')<figure class="brochure-figure"><a href="{{ asset('assets/brosur-2026-2027.jpg') }}" target="_blank" rel="noopener"><img src="{{ asset('assets/brosur-2026-2027.jpg') }}" alt="Brosur resmi Ibnu Aqil tahun ajaran 2027/2028, enam unit pendidikan dan layanan" width="6000" height="1156" loading="lazy"></a><figcaption>Brosur resmi TP 2027/2028. Klik untuk memperbesar. Ketersediaan, jadwal, dan ketentuan pendaftaran dikonfirmasi melalui admin.</figcaption></figure>@endif
    @if($page->slug === 'pendaftaran' && !empty($settings['registration_url']) && preg_match('/^https:\/\//i', $settings['registration_url']))<div class="registration-action"><a href="{{ $settings['registration_url'] }}" class="button" target="_blank" rel="noopener noreferrer">Buka layanan pendaftaran <span aria-hidden="true">↗</span></a><p>Tautan pendaftaran dibuka di tab baru.</p></div>@endif
    @if($page->slug === 'tentang')@include('public.partials.highlights')@endif
    @if($page->slug === 'fasilitas')<h2 class="page-subheading">Fasilitas dalam foto</h2>@include('public.partials.facility-grid')@endif
    @if($page->slug === 'pendaftaran')<h2 class="page-subheading">Alur pendaftaran</h2>@include('public.partials.admission-steps')@endif
</article><aside class="page-aside"><p class="eyebrow">Jelajahi Ibnu Aqil</p><nav aria-label="Halaman informasi">@foreach(['tentang' => 'Tentang lembaga', 'fasilitas' => 'Fasilitas & program', 'prestasi' => 'Prestasi', 'pendaftaran' => 'Informasi pendaftaran', 'kontak' => 'Hubungi kami'] as $slug => $label)<a href="{{ route('public.page', $slug) }}" @if($page->slug === $slug) aria-current="page" @endif>{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</nav>@if($asideWa = \App\Services\WhatsApp::link($settings ?? [], 'Saya ingin bertanya tentang pendaftaran di Ibnu Aqil.'))<div class="aside-note"><span aria-hidden="true">↗</span><h2>Ada pertanyaan<br>tentang pendaftaran?</h2><a class="text-link" href="{{ $asideWa }}" target="_blank" rel="noopener noreferrer">Tanya admin PPDB <span aria-hidden="true">→</span></a></div>@endif</aside></div></section>
@if($page->slug === 'tentang')
@include('public.partials.stats-band')
@include('public.partials.founder')
@endif
@endsection
