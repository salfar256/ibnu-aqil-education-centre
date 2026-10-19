<?php

namespace App\Http\Controllers;

use App\Models\{AccessGrant, Activity, Article, Asset, AuditLog, ContentPage, Material, School, Setting, User};
use App\Services\{AssetStorage, Audit, SocialLinks};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Hash, Password, Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\{Rule, ValidationException};
use Illuminate\View\View;

class AdminController extends Controller
{
    private const RESOURCES = [
        'schools' => [School::class, 'Sekolah mitra', 'sekolah', ['name' => 'Nama sekolah', 'code' => 'Kode', 'is_active' => 'Status']],
        'users' => [User::class, 'Pengguna', 'pengguna', ['name' => 'Nama', 'email' => 'Email', 'role' => 'Peran', 'is_active' => 'Status']],
        'activities' => [Activity::class, 'Kegiatan', 'kegiatan', ['title' => 'Kegiatan', 'theme' => 'Tema', 'year' => 'Tahun', 'status' => 'Status']],
        'materials' => [Material::class, 'Materi pengimbasan', 'materi', ['title' => 'Materi', 'type' => 'Jenis', 'author_name' => 'Pembuat', 'status' => 'Status']],
        'grants' => [AccessGrant::class, 'Hak akses sekolah', 'hak akses', ['school_label' => 'Sekolah', 'activity_label' => 'Kegiatan', 'expires_at' => 'Berlaku sampai', 'is_active' => 'Status']],
        'pages' => [ContentPage::class, 'Halaman & profil unit', 'halaman', ['title' => 'Halaman', 'type' => 'Jenis', 'status' => 'Status', 'verified_at' => 'Diverifikasi']],
        'articles' => [Article::class, 'Berita, galeri & prestasi', 'konten', ['title' => 'Judul', 'kind' => 'Jenis', 'status' => 'Status', 'published_at' => 'Tanggal terbit']],
        'settings' => [Setting::class, 'Kontak & pendaftaran', 'pengaturan', []],
        'logs' => [AuditLog::class, 'Catatan aktivitas', 'catatan', ['created_at' => 'Waktu', 'user_id' => 'ID pengguna', 'school_id' => 'ID sekolah', 'action' => 'Aktivitas', 'subject_type' => 'Objek', 'subject_id' => 'ID objek']],
    ];

    public function index(Request $request): View
    {
        $isAdmin = $request->user()->role === 'admin';
        $stats = [
            ['Kegiatan', Activity::count(), 'activities'],
            ['Materi terbit', Material::where('status', 'published')->count(), 'materials'],
            ['Materi draf', Material::where('status', 'draft')->count(), 'materials'],
        ];
        if ($isAdmin) {
            $stats[] = ['Sekolah aktif', School::where('is_active', true)->count(), 'schools'];
            $stats[] = ['Pengguna aktif', User::where('is_active', true)->count(), 'users'];
            $stats[] = ['Hak akses aktif', AccessGrant::where('is_active', true)->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(), 'grants'];
        }
        return view('admin.dashboard', [
            'stats' => $stats,
            'assetCount' => Asset::count(),
            'assetBytes' => Asset::sum('size'),
            'uploadMaxKb' => (int) config('portal.upload_max_kb', 10240),
            'recentMaterials' => Material::latest('id')->limit(5)->get(),
            'unverifiedCount' => $isAdmin ? ContentPage::whereNull('verified_at')->count() + Article::whereNull('verified_at')->count() : null,
        ]);
    }

    public function list(Request $request, string $resource): View
    {
        $meta = $this->resource($request, $resource);
        if ($resource === 'settings') {
            return view('admin.settings', ['title' => $meta[1], 'values' => Setting::pluck('value', 'key'), 'fields' => $this->settingFields()]);
        }
        $query = $meta[0]::query();
        if ($resource === 'grants') {
            $query->with(['school', 'activity']);
        }
        $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $search = trim((string) $request->query('q', ''));
        if ($search !== '' && $resource !== 'logs') {
            $query->where(function ($q) use ($resource, $search) {
                if ($resource === 'grants') {
                    $q->whereHas('school', fn ($s) => $s->where('name', 'like', '%'.$search.'%'))
                        ->orWhereHas('activity', fn ($a) => $a->where('title', 'like', '%'.$search.'%'));
                } else {
                    $field = in_array($resource, ['schools', 'users'], true) ? 'name' : 'title';
                    $q->where($field, 'like', '%'.$search.'%');
                    if ($resource === 'users') {
                        $q->orWhere('email', 'like', '%'.$search.'%');
                    }
                }
            });
        }
        $records = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.index', compact('resource', 'meta', 'records', 'search'));
    }

    public function create(Request $request, string $resource): View
    {
        $meta = $this->resource($request, $resource, true);
        return $this->form($resource, new $meta[0], $meta);
    }

    public function edit(Request $request, string $resource, int $id): View
    {
        $meta = $this->resource($request, $resource, true);
        return $this->form($resource, $meta[0]::findOrFail($id), $meta);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $meta = $this->resource($request, $resource);
        if ($resource === 'settings') {
            return $this->saveSettings($request);
        }
        abort_if($resource === 'logs', 405);
        $record = new $meta[0];
        $data = $this->validated($request, $resource, $record);
        if ($resource === 'users') {
            $data['password'] = Hash::make(Str::random(64));
            $data['must_change_password'] = true;
        }
        $record->fill($data)->save();
        Audit::record('admin.'.$resource.'.created', $record);
        $notice = 'Data berhasil ditambahkan.';
        if ($resource === 'users' && $request->boolean('send_reset')) {
            $notice .= ' '.$this->resetNotice($record);
        }
        return redirect()->route('admin.resources.edit', [$resource, $record->getKey()])->with('status', $notice);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $meta = $this->resource($request, $resource, true);
        $record = $meta[0]::findOrFail($id);
        $data = $this->validated($request, $resource, $record);
        if ($resource === 'users' && (int) $record->id === (int) $request->user()->id) {
            if ($data['role'] !== $record->role || ! $data['is_active']) {
                throw ValidationException::withMessages(['role' => 'Peran dan status aktif akun Anda sendiri tidak dapat diubah di sini.']);
            }
        }
        $record->fill($data)->save();
        Audit::record('admin.'.$resource.'.updated', $record);
        return back()->with('status', 'Perubahan berhasil disimpan.');
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        $meta = $this->resource($request, $resource, true);
        $record = $meta[0]::findOrFail($id);
        if ($resource === 'users' && (int) $record->id === (int) $request->user()->id) {
            throw ValidationException::withMessages(['record' => 'Akun Anda sendiri tidak dapat dinonaktifkan.']);
        }
        $field = in_array($resource, ['schools', 'users', 'grants'], true) ? 'is_active' : 'status';
        $record->{$field} = $field === 'is_active' ? false : 'draft';
        $record->save();
        Audit::record('admin.'.$resource.'.withdrawn', $record);
        return back()->with('status', $resource === 'grants' ? 'Hak akses dicabut.' : 'Data dinonaktifkan atau dikembalikan ke draf.');
    }

    public function storeAssets(Request $request, AssetStorage $storage): RedirectResponse
    {
        $this->resource($request, 'materials');
        $data = $request->validate([
            'material_id' => ['nullable', 'integer', 'exists:materials,id'],
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:'.(int) config('portal.upload_max_kb', 10240)],
            'allow_download' => ['nullable', 'boolean'],
        ]);
        $material = ! empty($data['material_id']) ? Material::findOrFail($data['material_id']) : null;
        abort_if(! $material && $request->user()->role !== 'admin', 403);
        if (! $material) {
            $request->validate(['files.*' => ['mimetypes:image/jpeg,image/png,image/webp']]);
        }
        // Validate and store each asset through the same MIME-aware storage service.
        $stored = [];
        try {
            foreach ($request->file('files', []) as $file) {
                $asset = $storage->store($file, $material, $material ? 'private' : 'public', $request->boolean('allow_download'));
                $stored[] = $asset;
            }
        } catch (\Throwable $exception) {
            foreach ($stored as $asset) {
                Storage::disk('private')->delete($asset->path);
                $asset->delete();
            }
            throw $exception;
        }
        foreach ($stored as $asset) {
            Audit::record('admin.asset.uploaded', $asset);
        }
        return back()->with('status', count($stored).' lampiran berhasil diunggah.');
    }

    public function destroyAsset(Request $request, Asset $asset): RedirectResponse
    {
        $this->resource($request, 'materials');
        abort_if(! $asset->material_id && $request->user()->role !== 'admin', 403);
        if (Article::where('cover_asset_id', $asset->id)->exists()) {
            throw ValidationException::withMessages(['files' => 'File masih digunakan sebagai sampul. Ubah sampul konten sebelum menghapusnya.']);
        }
        if (Storage::disk('private')->exists($asset->path) && ! Storage::disk('private')->delete($asset->path)) {
            throw ValidationException::withMessages(['files' => 'File gagal dihapus dari penyimpanan. Coba kembali atau periksa izin folder.']);
        }
        Audit::record('admin.asset.deleted', $asset);
        $asset->delete();
        return back()->with('status', 'Lampiran dihapus.');
    }

    public function sendReset(Request $request, User $user): RedirectResponse
    {
        $this->resource($request, 'users');
        abort_unless($user->is_active, 422, 'Aktifkan pengguna sebelum mengirim tautan pemulihan.');
        Audit::record('admin.user.reset_requested', $user);
        return back()->with('status', $this->resetNotice($user));
    }

    private function resetNotice(User $user): string
    {
        try {
            $status = Password::sendResetLink(['email' => $user->email]);
            return $status === Password::RESET_LINK_SENT
                ? 'Tautan untuk mengatur kata sandi telah dikirim ke email pengguna.'
                : ($status === Password::RESET_THROTTLED ? 'Tautan baru saja dikirim. Tunggu sebelum mencoba lagi.' : 'Email pemulihan belum terkirim. Periksa konfigurasi email dan gunakan kembali tombol kirim tautan.');
        } catch (\Throwable $exception) {
            report($exception);
            return 'Akun tersimpan; email pemulihan belum terkirim. Periksa konfigurasi SMTP, lalu kirim ulang tautan.';
        }
    }

    private function resource(Request $request, string $resource, bool $writable = false): array
    {
        abort_unless(isset(self::RESOURCES[$resource]), 404);
        abort_unless(in_array($request->user()->role, ['admin', 'editor'], true), 403);
        abort_if($request->user()->role !== 'admin' && ! in_array($resource, ['activities', 'materials'], true), 403);
        abort_if($writable && in_array($resource, ['logs', 'settings'], true), 405);
        return self::RESOURCES[$resource];
    }

    private function validated(Request $request, string $resource, Model $record): array
    {
        if ($resource === 'users' && is_string($request->input('email'))) { $request->merge(['email' => Str::lower(trim($request->input('email')))]); }
        if (in_array($resource, ['activities', 'materials', 'pages', 'articles'], true)) {
            $rawSlug = $request->input('slug') ?: $request->input('title');
            $request->merge(['slug' => is_string($rawSlug) ? Str::slug($rawSlug) : $rawSlug]);
        }
        $unique = fn (string $table, string $field) => Rule::unique($table, $field)->ignore($record->getKey());
        $status = ['required', Rule::in(['draft', 'published'])];
        $text = ['nullable', 'string', 'max:50000'];
        $title = ['required', 'string', 'max:200'];
        $slug = ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];
        $rules = match ($resource) {
            'schools' => ['name' => $title, 'code' => ['required', 'string', 'max:40', $unique('schools', 'code')], 'is_active' => ['nullable', 'boolean']],
            'users' => ['name' => $title, 'email' => ['required', 'email', 'max:254', $unique('users', 'email')], 'school_id' => ['required', 'integer', 'exists:schools,id'], 'role' => ['required', Rule::in(['admin', 'editor', 'partner'])], 'is_active' => ['nullable', 'boolean'], 'send_reset' => ['nullable', 'boolean']],
            'activities' => ['title' => $title, 'slug' => [...$slug, $unique('activities', 'slug')], 'theme' => ['required', 'string', 'max:150'], 'year' => ['required', 'integer', 'between:2000,2100'], 'description' => $text, 'status' => $status],
            'materials' => ['activity_id' => ['required', 'integer', 'exists:activities,id'], 'title' => $title, 'slug' => [...$slug, $unique('materials', 'slug')], 'description' => $text, 'type' => ['required', Rule::in(['pdf', 'ebook', 'module', 'photo', 'video'])], 'author_name' => ['required', 'string', 'max:150'], 'published_at' => ['nullable', 'date'], 'status' => $status, 'video_provider' => ['nullable', Rule::in(['youtube', 'vimeo'])], 'video_id' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_-]+$/'], 'video_protection' => ['required', Rule::in(['link', 'controlled'])]],
            'grants' => ['school_id' => ['required', 'integer', 'exists:schools,id'], 'activity_id' => ['required', 'integer', 'exists:activities,id', Rule::unique('access_grants', 'activity_id')->where('school_id', $request->input('school_id'))->ignore($record->getKey())], 'expires_at' => ['nullable', 'date'], 'allow_download' => ['nullable', 'boolean'], 'is_active' => ['nullable', 'boolean']],
            'pages' => ['title' => $title, 'slug' => [...$slug, $unique('content_pages', 'slug')], 'summary' => ['nullable', 'string', 'max:500'], 'body' => $text, 'type' => ['required', Rule::in(['unit', 'page'])], 'status' => $status, 'source_note' => ['nullable', 'string', 'max:2000'], 'verified' => ['nullable', 'boolean']],
            'articles' => ['kind' => ['required', Rule::in(['news', 'gallery', 'achievement'])], 'title' => $title, 'slug' => [...$slug, $unique('articles', 'slug')], 'summary' => ['nullable', 'string', 'max:500'], 'body' => $text, 'status' => $status, 'published_at' => ['nullable', 'date'], 'verified' => ['nullable', 'boolean'], 'cover_asset_id' => ['nullable', 'integer', Rule::exists('assets', 'id')->where('visibility', 'public')->where('mime_type', 'like', 'image/%')]],
            default => [],
        };
        $data = $request->validate($rules);
        foreach (['is_active', 'allow_download'] as $field) {
            if (array_key_exists($field, $rules)) {
                $data[$field] = $request->boolean($field);
            }
        }
        unset($data['send_reset']);
        if (in_array($resource, ['pages', 'articles'], true)) {
            if ($data['status'] === 'published' && ! $request->boolean('verified')) {
                throw ValidationException::withMessages(['verified' => 'Verifikasi isi dan sumber informasi sebelum menerbitkan.']);
            }
            $data['verified_at'] = $request->boolean('verified') ? now() : null;
            unset($data['verified']);
        }
        if ($resource === 'materials') {
            $data['description'] = $data['description'] ?? '';
            if ($data['type'] === 'video' && (! $request->filled('video_provider') || ! $request->filled('video_id'))) {
                throw ValidationException::withMessages(['video_id' => 'Pilih penyedia dan isi ID video yang sah.']);
            }
            if ($data['type'] === 'video') {
                $pattern = $data['video_provider'] === 'youtube' ? '/^[A-Za-z0-9_-]{11}$/' : '/^[0-9]{6,12}$/';
                if (! preg_match($pattern, (string) ($data['video_id'] ?? ''))) {
                    throw ValidationException::withMessages(['video_id' => $data['video_provider'] === 'youtube' ? 'ID YouTube harus terdiri dari 11 karakter.' : 'ID Vimeo harus berupa 6–12 angka.']);
                }
            }
            if ($data['type'] === 'video' && $data['video_protection'] === 'controlled' && $data['status'] === 'published' && ! config('portal.controlled_video_enabled', false)) {
                throw ValidationException::withMessages(['video_protection' => 'Penyedia video dengan kontrol akses belum dikonfigurasi. Simpan sebagai draf.']);
            }
            if ($data['type'] !== 'video') {
                $data['video_provider'] = null;
                $data['video_id'] = null;
            }
        }
        if ($resource === 'articles') {
            $data['summary'] = $data['summary'] ?? '';
            $data['body'] = $data['body'] ?? '';
        }
        if (in_array($resource, ['materials', 'articles'], true) && $data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        return $data;
    }

    private function form(string $resource, Model $record, array $meta): View
    {
        $schools = in_array($resource, ['users', 'grants'], true) ? School::orderBy('name')->pluck('name', 'id') : collect();
        $activities = in_array($resource, ['materials', 'grants'], true) ? Activity::orderByDesc('year')->orderBy('title')->pluck('title', 'id') : collect();
        $covers = $resource === 'articles' ? Asset::where('visibility', 'public')->where('mime_type', 'like', 'image/%')->orderByDesc('id')->pluck('original_name', 'id') : collect();
        $assets = $resource === 'materials' && $record->exists ? $record->assets()->orderByDesc('id')->get() : collect();
        return view('admin.form', compact('resource', 'record', 'meta', 'schools', 'activities', 'covers', 'assets'));
    }

    private function settingFields(): array
    {
        return [
            'contact_email' => ['Email resmi', 'email'],
            'contact_phone' => ['Nomor telepon resmi', 'text'],
            'whatsapp_url' => ['Tautan WhatsApp PPDB', 'url'],
            'registration_url' => ['Tautan sistem pendaftaran', 'url'],
            'address' => ['Alamat resmi', 'textarea'],
            'maps_url' => ['Tautan Google Maps', 'url'],
            'instagram_url' => ['Tautan Instagram', 'url'],
            'youtube_url' => ['Tautan YouTube', 'url'],
            'tiktok_url' => ['Tautan TikTok', 'url'],
        ];
    }

    private function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'contact_email' => ['nullable', 'email', 'max:254'],
            'contact_phone' => ['nullable', 'string', 'max:60', 'regex:/^[0-9+() .-]+$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'whatsapp_url' => ['nullable', 'url:https', 'max:500', 'regex:~^https://(?:wa\.me/[0-9]+|api\.whatsapp\.com/send\?[^\s]+)$~i'],
            'registration_url' => ['nullable', 'url:https', 'max:500'],
            'maps_url' => ['nullable', 'url:https', 'max:500', 'regex:'.SocialLinks::PATTERNS['maps_url']],
            'instagram_url' => ['nullable', 'url:https', 'max:500', 'regex:'.SocialLinks::PATTERNS['instagram_url']],
            'youtube_url' => ['nullable', 'url:https', 'max:500', 'regex:'.SocialLinks::PATTERNS['youtube_url']],
            'tiktok_url' => ['nullable', 'url:https', 'max:500', 'regex:'.SocialLinks::PATTERNS['tiktok_url']],
            'verified' => ['accepted'],
        ]);
        foreach (array_keys($this->settingFields()) as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? '']);
        }
        Audit::record('admin.settings.updated');
        return back()->with('status', 'Kontak resmi dan tautan pendaftaran disimpan.');
    }
}
