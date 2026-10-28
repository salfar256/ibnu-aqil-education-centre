@extends('layouts.public')
@section('title', 'Kontak — Ibnu Aqil Education Center')
@section('description', 'Hubungi Sekolah Ibnu Aqil: WhatsApp PPDB, telepon kantor, lokasi sekolah di Laladon, Ciomas, Bogor, dan media sosial resmi @ibnuaqilmedia.')
@php
    use App\Services\{SocialLinks, WhatsApp};
    $wa = WhatsApp::link($settings, 'Saya ingin bertanya tentang Sekolah Ibnu Aqil.');
    $visit = WhatsApp::link($settings, 'Saya ingin berkunjung ke sekolah Ibnu Aqil. Kapan waktu yang tepat?');
    $phone = $settings['contact_phone'] ?? '';
    $tel = $phone !== '' ? 'tel:'.preg_replace('/[^0-9+]/', '', $phone) : null;
    $maps = SocialLinks::maps($settings);
    $address = strtok((string) ($settings['contact_address'] ?? ''), "\n");
    $social = SocialLinks::social($settings);
    $waDisplay = preg_match('~^https://wa\.me/62([0-9]{8,13})$~', (string) ($settings['whatsapp_url'] ?? ''), $m) ? implode('-', str_split('0'.$m[1], 4)) : null;
    $kid = collect(['smp-2', 'mi-3', 'sma-1'])->first(fn ($k) => is_file(public_path('assets/foto/siswa/'.$k.'.webp')));
    $kidSize = $kid ? getimagesize(public_path('assets/foto/siswa/'.$kid.'.webp')) : null;
    $socialMeta = [
        'instagram_url' => ['ig', 'Foto & cerita harian'],
        'youtube_url' => ['yt', 'Video kegiatan & acara'],
        'tiktok_url' => ['tt', 'Video singkat seru'],
    ];
@endphp
@section('content')
<section class="ia-page-hero">
    <div class="container">
        <nav class="ia-crumb" aria-label="Jejak halaman"><a href="{{ route('public.home') }}">Beranda</a> <span aria-hidden="true">›</span> <span aria-current="page">Kontak</span></nav>
        <h1>Hubungi kami</h1>
        <p>Pilih cara yang paling mudah untuk Bapak/Ibu.</p>
    </div>
</section>

{{-- Kartu kontak besar --}}
<section class="ia-section">
    <div class="container">
        <div class="ia-contact-cards">
            @if($wa)<a class="ia-ccard ia-ccard-wa" href="{{ $wa }}" target="_blank" rel="noopener noreferrer"><span class="ia-ccard-icon">@include('public.partials.icon', ['name' => 'whatsapp', 'size' => 32])</span><strong>WhatsApp PPDB</strong><span>{{ $waDisplay ?? 'Chat admin' }}</span><em>Chat sekarang @include('public.partials.icon', ['name' => 'arrow', 'size' => 18])</em></a>@endif
            @if($tel)<a class="ia-ccard" href="{{ $tel }}"><span class="ia-ccard-icon">@include('public.partials.icon', ['name' => 'phone', 'size' => 32])</span><strong>Telepon kantor</strong><span>{{ $phone }}</span><em>Telepon @include('public.partials.icon', ['name' => 'arrow', 'size' => 18])</em></a>@endif
            @if($maps)<a class="ia-ccard" href="{{ $maps }}" target="_blank" rel="noopener noreferrer"><span class="ia-ccard-icon">@include('public.partials.icon', ['name' => 'pin', 'size' => 32])</span><strong>Lokasi sekolah</strong><span>{{ $address }}</span><em>Petunjuk arah @include('public.partials.icon', ['name' => 'arrow', 'size' => 18])</em></a>@endif
            @if($visit)<a class="ia-ccard" href="{{ $visit }}" target="_blank" rel="noopener noreferrer"><span class="ia-ccard-icon">@include('public.partials.icon', ['name' => 'calendar', 'size' => 32])</span><strong>Kunjungi sekolah</strong><span>Senin–Jumat · 08.00–15.00 WIB</span><em>Atur jadwal @include('public.partials.icon', ['name' => 'arrow', 'size' => 18])</em></a>@endif
        </div>
    </div>
</section>

{{-- Ajakan media sosial + anak semangat --}}
@if($social !== [])
<section class="ia-social-band">
    <div class="container ia-social-inner">
        <div class="ia-social-copy">
            <p class="ia-ppdb-tag">@include('public.partials.icon', ['name' => 'heart', 'size' => 16]) {{ '@ibnuaqilmedia' }}</p>
            <h2>Yuk, ikuti media sosial kami!</h2>
            <p>Lihat keseruan kegiatan, prestasi, dan info PPDB terbaru setiap hari.</p>
            <div class="ia-social-cards">
                @foreach($social as $key => $link)
                <a class="ia-scard ia-scard-{{ $socialMeta[$key][0] ?? 'x' }}" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }} {{ $link['handle'] }} (tab baru)">
                    <span class="ia-scard-icon">
                        @switch($key)
                            @case('instagram_url')<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></svg>@break
                            @case('youtube_url')<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><rect x="2.5" y="5.5" width="19" height="13" rx="4" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m10 9 5 3-5 3Z" fill="currentColor"/></svg>@break
                            @default<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><path d="M13.5 3.5v11.2a3.3 3.3 0 1 1-3.3-3.3m3.3-7.9c.4 2.6 2.2 4.4 4.8 4.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        @endswitch
                    </span>
                    <span class="ia-scard-text"><strong>{{ $link['label'] }}</strong><small>{{ $link['handle'] }} · {{ $socialMeta[$key][1] ?? '' }}</small></span>
                    <span class="ia-scard-go">Ikuti @include('public.partials.icon', ['name' => 'arrow', 'size' => 16])</span>
                </a>
                @endforeach
            </div>
        </div>
        @if($kid)
        <figure class="ia-social-kid"><img src="{{ asset('assets/foto/siswa/'.$kid.'.webp') }}" alt="Siswa Ibnu Aqil yang bersemangat" width="{{ $kidSize[0] }}" height="{{ $kidSize[1] }}" loading="lazy" decoding="async"><span class="ia-bubble">Follow ya! 👋</span></figure>
        @endif
    </div>
</section>
@endif

@if($page->body)
<section class="ia-section ia-fac-list">
    <div class="container"><p class="ia-contact-note">@include('public.partials.icon', ['name' => 'help', 'size' => 20]) {{ $page->body }}</p></div>
</section>
@endif
@endsection
