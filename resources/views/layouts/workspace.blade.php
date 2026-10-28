<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Pengelolaan') · Ibnu Aqil</title>
    <link rel="stylesheet" href="{{ asset('assets/workspace.css') }}">
    <script src="{{ asset('assets/workspace.js') }}" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Langsung ke isi</a>
<div class="workspace">
    <aside class="sidebar" id="workspace-navigation" aria-label="Navigasi pengelolaan">
        <a class="brand" href="{{ route('admin.index') }}"><img class="official-logo" src="{{ asset('assets/logo-yhasb.png') }}" alt="Logo Yayasan Haji Agus Salim Bogor" width="44" height="45"><span>Ibnu Aqil<small>Education Center</small></span></a>
        <div class="workspace-label">RUANG PENGELOLAAN</div>
        <nav>
            <a href="{{ route('admin.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.index')])><span aria-hidden="true">◫</span> Ringkasan</a>
            <div class="nav-caption">PENGIMBASAN</div>
            @foreach(['activities' => 'Kegiatan', 'materials' => 'Materi & lampiran'] as $key => $label)
                <a href="{{ route('admin.resources.index', $key) }}" @class(['nav-link', 'active' => request()->route('resource') === $key])><span aria-hidden="true">{{ $key === 'activities' ? '▣' : '▤' }}</span> {{ $label }}</a>
            @endforeach
            @if(auth()->user()->role === 'admin')
                @foreach(['schools' => 'Sekolah mitra', 'users' => 'Pengguna', 'grants' => 'Hak akses sekolah'] as $key => $label)
                    <a href="{{ route('admin.resources.index', $key) }}" @class(['nav-link', 'active' => request()->route('resource') === $key])><span aria-hidden="true">◇</span> {{ $label }}</a>
                @endforeach
                <div class="nav-caption">WEBSITE LEMBAGA</div>
                @foreach(['pages' => 'Halaman & profil unit', 'articles' => 'Berita, galeri & prestasi', 'settings' => 'Kontak & pendaftaran', 'logs' => 'Catatan aktivitas'] as $key => $label)
                    <a href="{{ route('admin.resources.index', $key) }}" @class(['nav-link', 'active' => request()->route('resource') === $key])><span aria-hidden="true">{{ $key === 'logs' ? '◷' : '◇' }}</span> {{ $label }}</a>
                @endforeach
            @endif
        </nav>
        <div class="sidebar-bottom"><a href="{{ route('portal.index') }}">Buka portal mitra <span aria-hidden="true">↗</span></a><a href="{{ route('public.home') }}">Lihat website publik <span aria-hidden="true">↗</span></a></div>
    </aside>
    <div class="workspace-body">
        <header class="workspace-topbar">
            <button class="mobile-menu button button-quiet" type="button" aria-controls="workspace-navigation" aria-expanded="false" id="navigation-toggle">☰ Menu</button>
            <div class="topbar-context"><span class="status-dot" aria-hidden="true"></span> Ruang kerja {{ auth()->user()->role === 'admin' ? 'admin lembaga' : 'pengelola materi' }}</div>
            <div class="account"><a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a><form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="logout-button">Keluar</button></form></div>
        </header>
        <main id="main" class="workspace-main" tabindex="-1">
            @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1"><strong>Periksa kembali formulir.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
            <footer class="workspace-footer">Ibnu Aqil Education Center <span>Ruang kerja aman · akses sesuai peran</span></footer>
        </main>
    </div>
</div>
</body>
</html>
