@extends('layouts.public')
@section('title', $unit->title.' — Ibnu Aqil Education Centre')
@section('description', $unit->summary ?: 'Kenali '.$unit->title.' melalui profil unit di Ibnu Aqil Education Centre.')
@section('content')
@php
    $unitLabels = ['mi' => 'MI', 'smp' => 'SMP', 'sma' => 'SMA', 'smk' => 'SMK', 'pesantren' => 'PP'];
    $profileBody = str_replace(["\r\n", "\r"], "\n", (string) $unit->body);
    $programs = [];
    $programNotes = [];
    $bodyParts = preg_split('/\n{2,}Program yang diperkenalkan\s*\n/u', $profileBody, 2);
    if (count($bodyParts) === 2) {
        foreach (explode("\n", $bodyParts[1]) as $programLine) {
            if (preg_match('/^\s*[•*\-]\s+(.+)$/u', $programLine, $programMatch)) {
                $programs[] = trim($programMatch[1]);
            } elseif (trim($programLine) !== '') {
                $programNotes[] = trim($programLine);
            }
        }
        if ($programs !== []) $profileBody = $bodyParts[0];
    }
    $unitLogo = \App\Services\PublicPhotos::unitLogo($unit->slug);
    $unitGallery = \App\Services\SchoolProfile::unitPhotos($unit->slug);
    $unitPhoto = $unitGallery[0] ?? \App\Services\PublicPhotos::get(match($unit->slug) { 'pesantren', 'mi' => 'masjid', default => 'ilustrasi' });
    $ppdbUrl = \App\Services\WhatsApp::link($settings, \App\Services\WhatsApp::unitMessage($unit->slug));
@endphp
<div class="unit-profile unit-profile-{{ $unit->slug }}">
<section class="unit-hero"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><a href="{{ route('public.home') }}#unit-ibnu-aqil">Unit Ibnu Aqil</a><span aria-hidden="true">/</span><span aria-current="page">{{ $unitLabels[$unit->slug] ?? $unit->title }}</span></nav><div class="unit-hero-grid"><div>@if($unitLogo)<img class="unit-hero-logo" src="{{ asset($unitLogo) }}" alt="Logo {{ $unit->title }}" width="96" height="96" decoding="async">@endif<p class="eyebrow">Bagian dari Ibnu Aqil</p><h1>{{ $unit->title }}</h1>@if($unit->summary)<p class="page-lead">{{ $unit->summary }}</p>@endif<div class="hero-actions">@if($ppdbUrl)<a class="button button-whatsapp button-inline" href="{{ $ppdbUrl }}" target="_blank" rel="noopener noreferrer">Tanya PPDB {{ \App\Services\WhatsApp::UNITS[$unit->slug][0] ?? '' }} via WhatsApp <span aria-hidden="true">↗</span></a>@endif<a class="text-link" href="{{ route('public.page', 'pendaftaran') }}">Informasi pendaftaran <span aria-hidden="true">→</span></a></div>@if($programs !== [])<a class="unit-program-jump" href="#program-unit">Jelajahi program unit <span aria-hidden="true">↓</span></a>@endif</div>@if($unitPhoto)<figure class="unit-photo" data-reveal data-tilt><img src="{{ $unitPhoto['src'] }}" alt="{{ $unitPhoto['alt'] }}" width="{{ $unitPhoto['width'] }}" height="{{ $unitPhoto['height'] }}" decoding="async"><figcaption><strong>{{ $unitLabels[$unit->slug] ?? 'IA' }}</strong><span>{{ $unitPhoto['tag'] ?? $unitPhoto['caption'] }}</span></figcaption></figure>@else<div class="unit-emblem" aria-label="Tipografi unit, bukan logo resmi" data-reveal><span>IBNU AQIL</span><strong>{{ $unitLabels[$unit->slug] ?? 'IA' }}</strong><small>EDUCATION CENTRE</small></div>@endif</div></div></section>
<section class="section page-section"><div class="container content-layout"><article class="prose" data-reveal><p class="eyebrow">Mengenal unit</p><h2>Profil {{ $unit->title }}</h2>@if($profileBody)<div class="content-body">{!! nl2br(e($profileBody)) !!}</div>@else<p class="content-status">Informasi profil unit belum diterbitkan.</p>@endif
    @if($programs !== [])<section class="unit-program-section" id="program-unit" aria-labelledby="program-heading"><p class="eyebrow">Program unit</p><h2 id="program-heading">Program yang diperkenalkan.</h2><ul class="unit-program-grid">@foreach($programs as $program)<li data-reveal><span class="program-bullet" aria-hidden="true">✳</span><span>{{ $program }}</span></li>@endforeach</ul>@foreach($programNotes as $note)<p class="unit-program-note">{{ $note }}</p>@endforeach</section>@endif
</article><aside class="page-aside"><p class="eyebrow">Unit lainnya</p><nav aria-label="Profil unit Ibnu Aqil">@foreach(['mi' => 'MI Ibnu Aqil', 'smp' => 'SMP Ibnu Aqil', 'sma' => 'SMA Ibnu Aqil', 'smk' => 'SMK Ibnu Aqil', 'pesantren' => 'Pondok Pesantren'] as $slug => $label)<a href="{{ route('public.unit', $slug) }}" @if($unit->slug === $slug) aria-current="page" @endif>{{ $label }} <span aria-hidden="true">↗</span></a>@endforeach</nav></aside></div></section>
@if(count($unitGallery) > 1)<section class="section section-activities" aria-labelledby="unit-gallery-heading"><div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Dokumentasi kegiatan</p><h2 id="unit-gallery-heading">Suasana belajar<br><em>{{ $unit->title }}.</em></h2></div><a class="text-link" href="{{ route('public.gallery') }}">Galeri lengkap <span aria-hidden="true">↗</span></a></div>@include('public.partials.activity-mosaic', ['photos' => $unitGallery])</div></section>@endif
@include('public.partials.cost-band', ['costUnit' => $unit->slug])
@include('public.partials.wa-faq', ['faqUnit' => $unit->slug])
<section class="unit-bottom"><div class="container"><div><p class="eyebrow">PPDB 2026/2027</p><h2>Siap bergabung dengan {{ $unit->title }}?</h2></div>@if($ppdbUrl)<a href="{{ $ppdbUrl }}" class="button button-whatsapp button-inline" target="_blank" rel="noopener noreferrer">Chat WhatsApp PPDB <span aria-hidden="true">↗</span></a>@else<a href="{{ route('public.page', 'kontak') }}" class="button">Hubungi pengelola <span aria-hidden="true">↗</span></a>@endif</div></section>
</div>
@endsection
