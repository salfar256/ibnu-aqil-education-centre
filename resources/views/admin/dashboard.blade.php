@extends('layouts.workspace')
@section('title', 'Ringkasan admin')
@section('content')
<div class="page-heading"><div><p class="eyebrow">PENGELOLAAN IBNU AQIL</p><h1>Setiap ilmu, lebih bermanfaat.</h1><p class="lead">Kelola kegiatan, bagikan praktik baik, dan hubungkan sekolah mitra.</p></div><a class="button button-primary" href="{{ route('admin.resources.create', 'materials') }}">+ Tambah materi</a></div>
<div class="stat-grid">
    @foreach($stats as [$label, $count, $resource])
        <a class="stat-card" href="{{ route('admin.resources.index', $resource) }}"><span class="stat-label">{{ $label }}</span><strong>{{ number_format($count, 0, ',', '.') }}</strong><span class="stat-link">Kelola <span aria-hidden="true">↗</span></span></a>
    @endforeach
</div>
<div class="dashboard-grid">
    <section class="panel"><div class="panel-heading"><div><p class="eyebrow">PERPUSTAKAAN PENGIMBASAN</p><h2>Materi terbaru</h2></div><a class="text-link" href="{{ route('admin.resources.index', 'materials') }}">Lihat semua →</a></div>
        @forelse($recentMaterials as $material)
            <a class="material-row" href="{{ route('admin.resources.edit', ['materials', $material->id]) }}"><span class="document-icon" aria-hidden="true">▤</span><span><strong>{{ $material->title }}</strong><small>{{ strtoupper($material->type) }} · {{ $material->author_name }}</small></span><span class="badge {{ $material->status === 'published' ? 'badge-green' : 'badge-amber' }}">{{ $material->status === 'published' ? 'Terbit' : 'Draf' }}</span></a>
        @empty
            <div class="empty-state"><span aria-hidden="true">▤</span><h3>Ruang berbagi dimulai di sini</h3><p>Buat kegiatan, tambahkan materi, lalu berikan akses kepada sekolah mitra.</p><a href="{{ route('admin.resources.create', 'activities') }}" class="button button-secondary">Buat kegiatan pertama</a></div>
        @endforelse
    </section>
    <div class="panel-stack">
        <section class="panel storage-panel"><p class="eyebrow">PENYIMPANAN LAMPIRAN</p><h2>{{ number_format($assetBytes / 1048576, 1, ',', '.') }} <small>MB</small></h2><progress max="{{ 15 * 1024 * 1024 * 1024 }}" value="{{ min($assetBytes, 15 * 1024 * 1024 * 1024) }}" aria-label="Ukuran lampiran tersimpan dibanding target hosting 15 GB"></progress><p>{{ $assetCount }} file tersimpan · batas unggah {{ number_format($uploadMaxKb / 1024, 1, ',', '.') }} MB per file.</p><small>Target hosting 15 GB mencakup aplikasi, database, email, dan backup. Angka ini menghitung lampiran yang tercatat.</small></section>
        @if($unverifiedCount !== null)
            <section class="panel note-panel"><p class="eyebrow">KONTEN RESMI</p><h2>Periksa sebelum terbit</h2><p><strong>{{ $unverifiedCount }} konten</strong> belum ditandai terverifikasi. Pastikan kontak, biaya, periode pendaftaran, dan klaim lembaga telah dikonfirmasi.</p><a href="{{ route('admin.resources.index', 'pages') }}" class="text-link">Tinjau halaman lembaga →</a></section>
        @endif
    </div>
</div>
<section class="workflow-strip"><span class="step-number">1</span><span>Buat kegiatan</span><span aria-hidden="true">→</span><span class="step-number">2</span><span>Unggah & terbitkan materi</span><span aria-hidden="true">→</span><span class="step-number">3</span><span>Berikan akses sekolah</span></section>
@endsection
