@extends('layouts.public')
@section('title', $article->title.' — Ibnu Aqil Education Centre')
@section('description', $article->summary ?: $article->title)
@section('og_type', 'article')
@section('content')
<section class="article-header"><div class="container"><nav class="breadcrumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a><span aria-hidden="true">/</span><a href="{{ route('public.news') }}">Berita & kegiatan</a><span aria-hidden="true">/</span><span aria-current="page">Artikel</span></nav><div class="article-header-inner"><p class="eyebrow">Cerita dari Ibnu Aqil</p><h1>{{ $article->title }}</h1>@if($article->summary)<p class="page-lead">{{ $article->summary }}</p>@endif<div class="article-meta"><span>Ibnu Aqil Education Centre</span>@if($article->published_at)<span aria-hidden="true">/</span><time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->translatedFormat('d F Y') }}</time>@endif</div></div></div></section>
<section class="article-section"><div class="container">@if($article->cover_asset_id)<figure class="article-cover"><img src="{{ url('/media/'.$article->cover_asset_id) }}" alt="{{ $article->title }}" width="1440" height="960"></figure>@endif<article class="prose article-prose"><div class="content-body">{!! nl2br(e($article->body)) !!}</div><div class="article-end"><span aria-hidden="true">✳</span><a class="text-link" href="{{ route('public.news') }}">← Kembali ke berita & kegiatan</a></div></article></div></section>
@endsection
