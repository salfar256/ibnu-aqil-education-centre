@extends('layouts.workspace')
@section('title', $meta[1])
@section('content')
<div class="page-heading"><div><p class="eyebrow">RUANG PENGELOLAAN</p><h1>{{ $meta[1] }}</h1><p class="lead">{{ $resource === 'logs' ? 'Riwayat akses dan perubahan untuk penelusuran aktivitas.' : 'Data tersimpan dengan aman. Perubahan hak akses berlaku pada pemeriksaan akses berikutnya.' }}</p></div>@if($resource !== 'logs')<a class="button button-primary" href="{{ route('admin.resources.create', $resource) }}">+ Tambah {{ $meta[2] }}</a>@endif</div>
@if($resource === 'grants')<div class="alert alert-info">Akses diberikan per sekolah dan kegiatan. Akun harus aktif, sekolah harus aktif, serta kegiatan dan materi harus terbit. Tanggal kedaluwarsa dapat dikosongkan.</div>@endif
@if($resource === 'users')<div class="alert alert-info">Gunakan akun individu untuk setiap pengguna. Undangan dikirim melalui tautan pengaturan kata sandi; tidak ada kata sandi bawaan.</div>@endif
<section class="panel table-panel">
    <div class="table-toolbar">@if($resource !== 'logs')<form method="get" class="search-form"><label for="resource-search" class="sr-only">Cari {{ $meta[1] }}</label><input id="resource-search" name="q" value="{{ $search }}" placeholder="Cari {{ strtolower($meta[1]) }}…" maxlength="150"><button class="button button-secondary" type="submit">Cari</button>@if($search)<a class="text-link" href="{{ route('admin.resources.index', $resource) }}">Reset</a>@endif</form>@else<span>Catatan terbaru</span>@endif<span class="result-count">{{ number_format($records->total(), 0, ',', '.') }} data</span></div>
    <div class="table-scroll"><table><thead><tr>@foreach($meta[3] as $key => $label)<th scope="col">{{ $label }}</th>@endforeach @if($resource !== 'logs')<th scope="col" class="actions">Tindakan</th>@endif</tr></thead><tbody>
    @forelse($records as $record)
        <tr>@foreach($meta[3] as $key => $label)<td>
            @if($key === 'is_active')<span class="badge {{ $record->is_active ? 'badge-green' : 'badge-gray' }}">{{ $record->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            @elseif($key === 'status')<span class="badge {{ $record->status === 'published' ? 'badge-green' : 'badge-amber' }}">{{ $record->status === 'published' ? 'Terbit' : 'Draf' }}</span>
            @elseif($key === 'school_label'){{ $record->school?->name ?? 'Sekolah dihapus' }}
            @elseif($key === 'activity_label'){{ $record->activity?->title ?? 'Kegiatan dihapus' }}
            @elseif(in_array($key, ['created_at', 'expires_at', 'published_at', 'verified_at'], true)){{ $record->{$key} ? \Carbon\Carbon::parse($record->{$key})->format('d/m/Y H:i') : ($key === 'expires_at' ? 'Tanpa batas' : '—') }}
            @elseif($key === 'role'){{ ['admin' => 'Admin lembaga', 'editor' => 'Pengelola materi', 'partner' => 'Sekolah mitra'][$record->role] ?? $record->role }}
            @elseif($key === 'kind'){{ ['news' => 'Berita', 'gallery' => 'Galeri', 'achievement' => 'Prestasi'][$record->kind] ?? $record->kind }}
            @elseif($key === 'type'){{ ['unit' => 'Profil unit', 'page' => 'Halaman', 'pdf' => 'PDF', 'ebook' => 'Ebook', 'module' => 'Modul', 'photo' => 'Foto', 'video' => 'Video'][$record->type] ?? $record->type }}
            @elseif($key === 'subject_type'){{ $record->subject_type ? class_basename($record->subject_type) : '—' }}
            @else{{ \Illuminate\Support\Str::limit((string) $record->{$key}, 90) ?: '—' }}@endif
        </td>@endforeach
        @if($resource !== 'logs')<td class="actions"><a class="button button-small button-secondary" href="{{ route('admin.resources.edit', [$resource, $record->id]) }}" aria-label="Ubah {{ $record->title ?? $record->name ?? 'hak akses' }}">Ubah</a><form method="post" action="{{ route('admin.resources.destroy', [$resource, $record->id]) }}" data-confirm="{{ $resource === 'grants' ? 'Cabut akses sekolah untuk kegiatan ini?' : 'Nonaktifkan data ini atau kembalikan ke draf?' }}">@csrf @method('delete')<button class="button button-small button-quiet" type="submit">{{ $resource === 'grants' ? 'Cabut' : (in_array($resource, ['schools','users'], true) ? 'Nonaktifkan' : 'Tarik ke draf') }}</button></form></td>@endif</tr>
    @empty<tr><td colspan="{{ count($meta[3]) + ($resource === 'logs' ? 0 : 1) }}"><div class="empty-state"><h2>{{ $search ? 'Tidak ada hasil' : 'Belum ada data' }}</h2><p>{{ $search ? 'Coba kata pencarian lain.' : 'Tambahkan data pertama menggunakan tombol di atas.' }}</p></div></td></tr>@endforelse
    </tbody></table></div>
    @if($records->hasPages())<nav class="pagination" aria-label="Halaman data">@if($records->onFirstPage())<span>Sebelumnya</span>@else<a href="{{ $records->previousPageUrl() }}">← Sebelumnya</a>@endif<span>{{ $records->currentPage() }} / {{ $records->lastPage() }}</span>@if($records->hasMorePages())<a href="{{ $records->nextPageUrl() }}">Berikutnya →</a>@else<span>Berikutnya</span>@endif</nav>@endif
</section>
@endsection
