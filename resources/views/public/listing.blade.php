@extends('layouts.public')
@php
    $listingTitle = match($kind) { 'galeri' => 'Galeri Ibnu Aqil', 'prestasi' => 'Prestasi', default => 'Berita & kegiatan' };
    $listingDescription = match($kind) { 'galeri' => 'Dokumentasi yang dibagikan dari lingkungan dan kegiatan Ibnu Aqil.', 'prestasi' => 'Informasi prestasi yang diterbitkan oleh pengelola Ibnu Aqil.', default => 'Cerita, kabar, dan kegiatan yang diterbitkan dari Ibnu Aqil.' };
@endphp
@section('title', $listingTitle.' — Ibnu Aqil Education Center')
@section('description', $listingDescription)
@section('content')
<section class="page-hero"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">{{ $listingTitle }}</span></nav><div class="page-hero-grid"><div data-reveal><p class="eyebrow">Cerita dari Ibnu Aqil</p><h1>{{ $listingTitle }}</h1><p class="page-lead">{{ $listingDescription }}</p></div><span class="page-ornament" aria-hidden="true">✳</span></div></div></section>
@if($kind === 'prestasi')<section class="section section-achievements"><div class="container"><div class="section-heading" data-reveal><div><p class="eyebrow">Rekam jejak</p><h2>Prestasi siswa<br><em>Ibnu Aqil.</em></h2></div><p>Prestasi akademik, keagamaan, dan olahraga yang tercantum dalam brosur resmi Ibnu Aqil.</p></div>@include('public.partials.achievements')</div></section>@endif
@endsection
