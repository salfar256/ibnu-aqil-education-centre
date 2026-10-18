@extends('layouts.public')
@php
    $listingTitle = match($kind) { 'galeri' => 'Galeri Ibnu Aqil', 'prestasi' => 'Prestasi', default => 'Berita & kegiatan' };
    $listingDescription = match($kind) { 'galeri' => 'Dokumentasi yang dibagikan dari lingkungan dan kegiatan Ibnu Aqil.', 'prestasi' => 'Informasi prestasi yang diterbitkan oleh pengelola Ibnu Aqil.', default => 'Cerita, kabar, dan kegiatan yang diterbitkan dari Ibnu Aqil.' };
@endphp
@section('title', $listingTitle.' — Ibnu Aqil Education Centre')
@section('description', $listingDescription)
@section('content')
<section class="page-hero"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">{{ $listingTitle }}</span></nav><div class="page-hero-grid"><div data-reveal><p class="eyebrow">Cerita dari Ibnu Aqil</p><h1>{{ $listingTitle }}</h1><p class="page-lead">{{ $listingDescription }}</p></div><span class="page-ornament" aria-hidden="true">✳</span></div></div></section>
<section class="section"><div class="container">
    @if($items->count())<div class="news-grid @if($kind === 'galeri') gallery-grid @endif">@foreach($items as $article)@include('public.partials.article-card', ['article' => $article])@endforeach</div><div class="pagination-wrap">{{ $items->links('public.partials.pagination') }}</div>
    @else<div class="empty-state"><span class="empty-mark" aria-hidden="true">✳</span><h2>Belum ada {{ $kind === 'galeri' ? 'galeri' : ($kind === 'prestasi' ? 'prestasi' : 'berita') }} yang diterbitkan.</h2><p>Silakan jelajahi profil unit dan informasi lainnya di Ibnu Aqil.</p><a class="button" href="{{ route('public.home') }}#unit-ibnu-aqil">Jelajahi unit Ibnu Aqil <span aria-hidden="true">↗</span></a></div>@endif
</div></section>
@endsection
