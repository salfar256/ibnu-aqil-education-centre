@extends('layouts.workspace')
@section('title', ($record->exists ? 'Ubah ' : 'Tambah ').$meta[2])
@section('content')
<a class="back-link" href="{{ route('admin.resources.index', $resource) }}">← Kembali ke {{ strtolower($meta[1]) }}</a>
<div class="page-heading"><div><p class="eyebrow">{{ strtoupper($meta[1]) }}</p><h1>{{ $record->exists ? 'Ubah' : 'Tambah' }} {{ $meta[2] }}</h1><p class="lead">Lengkapi informasi dengan cermat. Kolom bertanda * wajib diisi.</p></div></div>
<form method="post" action="{{ $record->exists ? route('admin.resources.update', [$resource, $record->id]) : route('admin.resources.store', $resource) }}" class="panel edit-panel">
    @csrf @if($record->exists) @method('put') @endif
    <div class="form-grid">
    @switch($resource)
        @case('schools')
            @include('admin.field', ['name'=>'name', 'label'=>'Nama sekolah', 'required'=>true])
            @include('admin.field', ['name'=>'code', 'label'=>'Kode sekolah', 'required'=>true, 'max'=>40, 'help'=>'Kode internal yang unik, misalnya MITRA-001.'])
            @include('admin.field', ['name'=>'is_active', 'label'=>'Sekolah aktif', 'type'=>'checkbox', 'default'=>true, 'help'=>'Sekolah nonaktif tidak dapat mengakses materi portal.'])
            @break
        @case('users')
            @include('admin.field', ['name'=>'name', 'label'=>'Nama lengkap', 'required'=>true])
            @include('admin.field', ['name'=>'email', 'label'=>'Email individu', 'type'=>'email', 'required'=>true, 'max'=>254])
            @include('admin.field', ['name'=>'school_id', 'label'=>'Sekolah', 'type'=>'select', 'required'=>true, 'options'=>$schools])
            @include('admin.field', ['name'=>'role', 'label'=>'Peran', 'type'=>'select', 'required'=>true, 'options'=>['partner'=>'Sekolah mitra','editor'=>'Pengelola materi','admin'=>'Admin lembaga'], 'default'=>'partner', 'help'=>'Pengelola materi tidak dapat mengubah pengguna, sekolah, dan hak akses.'])
            @include('admin.field', ['name'=>'is_active', 'label'=>'Akun aktif', 'type'=>'checkbox', 'default'=>true])
            @if(!$record->exists) @include('admin.field', ['name'=>'send_reset', 'label'=>'Kirim email undangan untuk mengatur kata sandi', 'type'=>'checkbox', 'default'=>true, 'help'=>'Pengguna membuat kata sandinya sendiri melalui tautan email. Pastikan pengiriman email sudah dikonfigurasi.']) @endif
            @break
        @case('activities')
            @include('admin.field', ['name'=>'title', 'label'=>'Nama kegiatan', 'required'=>true])
            @include('admin.field', ['name'=>'slug', 'label'=>'Slug URL', 'help'=>'Kosongkan untuk membuat dari judul. Gunakan huruf kecil, angka, dan tanda hubung.'])
            @include('admin.field', ['name'=>'theme', 'label'=>'Tema', 'required'=>true, 'max'=>150])
            @include('admin.field', ['name'=>'year', 'label'=>'Tahun', 'type'=>'number', 'required'=>true, 'min'=>2000, 'max'=>2100, 'default'=>now()->year])
            @include('admin.field', ['name'=>'description', 'label'=>'Deskripsi kegiatan', 'type'=>'textarea', 'wide'=>true])
            @include('admin.field', ['name'=>'status', 'label'=>'Status', 'type'=>'select', 'required'=>true, 'options'=>['draft'=>'Draf','published'=>'Terbit'], 'default'=>'draft'])
            @break
        @case('materials')
            @include('admin.field', ['name'=>'title', 'label'=>'Judul materi', 'required'=>true])
            @include('admin.field', ['name'=>'slug', 'label'=>'Slug URL', 'help'=>'Kosongkan untuk membuat dari judul.'])
            @include('admin.field', ['name'=>'activity_id', 'label'=>'Kegiatan', 'type'=>'select', 'required'=>true, 'options'=>$activities])
            @include('admin.field', ['name'=>'type', 'label'=>'Jenis materi', 'type'=>'select', 'required'=>true, 'options'=>['pdf'=>'PDF','ebook'=>'Ebook','module'=>'Modul','photo'=>'Foto','video'=>'Video'], 'default'=>'pdf'])
            @include('admin.field', ['name'=>'author_name', 'label'=>'Pembuat / narasumber', 'required'=>true, 'max'=>150])
            @include('admin.field', ['name'=>'published_at', 'label'=>'Tanggal materi', 'type'=>'datetime-local', 'help'=>'Kosongkan untuk menggunakan waktu penerbitan.'])
            @include('admin.field', ['name'=>'description', 'label'=>'Deskripsi materi', 'type'=>'textarea', 'wide'=>true])
            @include('admin.field', ['name'=>'video_provider', 'label'=>'Penyedia video', 'type'=>'select', 'options'=>['youtube'=>'YouTube','vimeo'=>'Vimeo'], 'help'=>'Hanya diperlukan untuk materi video.'])
            @include('admin.field', ['name'=>'video_id', 'label'=>'ID video', 'max'=>100, 'help'=>'Masukkan ID video saja, bukan URL lengkap atau kode embed.'])
            @include('admin.field', ['name'=>'video_protection', 'label'=>'Perlindungan video', 'type'=>'select', 'required'=>true, 'options'=>['link'=>'Tautan tersembunyi (unlisted)','controlled'=>'Penyedia dengan kontrol akses'], 'default'=>'link', 'help'=>'Tautan unlisted dapat dibagikan kembali. Kontrol akses memerlukan penyedia yang dikonfigurasi; materi dapat tetap disalin atau direkam.'])
            @include('admin.field', ['name'=>'status', 'label'=>'Status', 'type'=>'select', 'required'=>true, 'options'=>['draft'=>'Draf','published'=>'Terbit'], 'default'=>'draft', 'help'=>'Materi hanya terlihat ketika kegiatannya terbit dan sekolah mempunyai hak akses aktif.'])
            @break
        @case('grants')
            @include('admin.field', ['name'=>'school_id', 'label'=>'Sekolah mitra', 'type'=>'select', 'required'=>true, 'options'=>$schools])
            @include('admin.field', ['name'=>'activity_id', 'label'=>'Kegiatan yang diizinkan', 'type'=>'select', 'required'=>true, 'options'=>$activities])
            @include('admin.field', ['name'=>'expires_at', 'label'=>'Berlaku sampai', 'type'=>'datetime-local', 'help'=>'Kosongkan untuk akses tanpa batas waktu. Waktu mengikuti zona aplikasi.'])
            @include('admin.field', ['name'=>'is_active', 'label'=>'Hak akses aktif', 'type'=>'checkbox', 'default'=>true])
            @include('admin.field', ['name'=>'allow_download', 'label'=>'Sekolah boleh mengunduh lampiran', 'type'=>'checkbox', 'default'=>false, 'help'=>'File juga harus mengizinkan unduhan. Tampilan dokumen tidak dapat mencegah tangkapan layar atau penyalinan.'])
            @break
        @case('pages')
            @include('admin.field', ['name'=>'title', 'label'=>'Judul halaman / profil', 'required'=>true])
            @include('admin.field', ['name'=>'slug', 'label'=>'Slug URL', 'help'=>'Gunakan slug halaman yang sudah ada agar tautan navigasi tetap sesuai.'])
            @include('admin.field', ['name'=>'type', 'label'=>'Jenis halaman', 'type'=>'select', 'required'=>true, 'options'=>['page'=>'Halaman umum','unit'=>'Profil unit'], 'default'=>'page'])
            @include('admin.field', ['name'=>'status', 'label'=>'Status', 'type'=>'select', 'required'=>true, 'options'=>['draft'=>'Draf','published'=>'Terbit'], 'default'=>'draft'])
            @include('admin.field', ['name'=>'summary', 'label'=>'Ringkasan / deskripsi pencarian', 'type'=>'textarea', 'rows'=>3, 'max'=>500, 'wide'=>true])
            @include('admin.field', ['name'=>'body', 'label'=>'Isi halaman', 'type'=>'textarea', 'rows'=>14, 'wide'=>true, 'help'=>'Tulis sebagai teks biasa. Pisahkan paragraf dengan baris kosong.'])
            @include('admin.field', ['name'=>'source_note', 'label'=>'Catatan sumber untuk admin', 'type'=>'textarea', 'max'=>2000, 'rows'=>3, 'wide'=>true, 'help'=>'Catat sumber serta tanggal konfirmasi. Catatan ini bukan konten publik.'])
            @include('admin.field', ['name'=>'verified', 'label'=>'Isi dan sumber sudah saya verifikasi sebagai informasi resmi', 'type'=>'checkbox', 'value'=>(bool)$record->verified_at, 'wide'=>true, 'help'=>'Periksa ulang biaya, tahun pendaftaran, kontak, akreditasi, dan klaim sebelum menerbitkan.'])
            @break
        @case('articles')
            @include('admin.field', ['name'=>'title', 'label'=>'Judul', 'required'=>true])
            @include('admin.field', ['name'=>'slug', 'label'=>'Slug URL', 'help'=>'Kosongkan untuk membuat dari judul.'])
            @include('admin.field', ['name'=>'kind', 'label'=>'Jenis konten', 'type'=>'select', 'required'=>true, 'options'=>['news'=>'Berita & kegiatan','gallery'=>'Galeri','achievement'=>'Prestasi'], 'default'=>'news'])
            @include('admin.field', ['name'=>'published_at', 'label'=>'Tanggal terbit', 'type'=>'datetime-local'])
            @include('admin.field', ['name'=>'summary', 'label'=>'Ringkasan', 'type'=>'textarea', 'max'=>500, 'rows'=>3, 'wide'=>true])
            @include('admin.field', ['name'=>'body', 'label'=>'Isi / keterangan foto', 'type'=>'textarea', 'rows'=>12, 'wide'=>true, 'help'=>'Gunakan teks biasa dan foto yang memiliki izin penggunaan. Jangan mengarang prestasi atau testimoni.'])
            @include('admin.field', ['name'=>'cover_asset_id', 'label'=>'Foto sampul / foto galeri', 'type'=>'select', 'options'=>$covers, 'help'=>'Unggah foto publik di bagian bawah halaman ini, lalu pilih sebagai sampul.'])
            @include('admin.field', ['name'=>'status', 'label'=>'Status', 'type'=>'select', 'required'=>true, 'options'=>['draft'=>'Draf','published'=>'Terbit'], 'default'=>'draft'])
            @include('admin.field', ['name'=>'verified', 'label'=>'Isi, foto, dan klaim sudah saya verifikasi', 'type'=>'checkbox', 'value'=>(bool)$record->verified_at, 'wide'=>true])
            @break
    @endswitch
    </div>
    <div class="form-actions"><button class="button button-primary" type="submit">{{ $record->exists ? 'Simpan perubahan' : 'Simpan '.$meta[2] }}</button><a class="button button-quiet" href="{{ route('admin.resources.index', $resource) }}">Batal</a></div>
</form>
@if($resource === 'users' && $record->exists)<section class="panel supplementary-panel"><h2>Pengaturan kata sandi</h2><p>Pengguna mengatur sendiri kata sandinya melalui tautan yang dikirim ke {{ $record->email }}. Tautan memiliki masa berlaku dan hanya dapat digunakan sekali.</p><form method="post" action="{{ route('admin.users.reset', $record) }}">@csrf<button class="button button-secondary" type="submit">Kirim tautan pengaturan kata sandi</button></form></section>@endif
@if($resource === 'materials' && $record->exists)
    <section class="panel supplementary-panel"><h2>Lampiran privat</h2><p>Lampiran hanya dibaca melalui pemeriksaan izin sekolah. Unggah PDF atau foto; video disimpan pada penyedia video terpisah.</p>
        @if($assets->isNotEmpty())<div class="attachment-list">@foreach($assets as $asset)<div class="attachment-row"><span><strong>{{ $asset->original_name }}</strong><small>{{ number_format($asset->size / 1048576, 2, ',', '.') }} MB · {{ $asset->allow_download ? 'Unduhan diizinkan jika hak akses sekolah mengizinkan' : 'Hanya tampilan' }}</small></span><form method="post" action="{{ route('admin.assets.destroy', $asset) }}" data-confirm="Hapus lampiran ini secara permanen?">@csrf @method('delete')<button class="button button-small button-danger" type="submit">Hapus</button></form></div>@endforeach</div>@endif
        @include('admin.upload', ['materialId'=>$record->id])
    </section>
@elseif($resource === 'materials')<div class="alert alert-info">Simpan materi terlebih dahulu, kemudian unggah lampiran di halaman ini.</div>@endif
@if($resource === 'articles')<section class="panel supplementary-panel"><h2>Unggah foto publik</h2><p>Foto yang dipilih sebagai sampul akan digunakan oleh berita, galeri, atau prestasi. Gunakan dokumentasi asli dengan izin penggunaan.</p>@include('admin.upload', ['materialId'=>null])</section>@endif
@endsection
