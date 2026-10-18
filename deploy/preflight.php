<?php

declare(strict_types=1);

// CLI-only: do not expose configuration, paths, or errors through HTTP.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$failed = 0;
$warned = 0;
$check = static function (bool $ok, string $message, bool $warning = false) use (&$failed, &$warned): void {
    if (! $ok) { $warning ? $warned++ : $failed++; }
    echo ($ok ? '[OK]   ' : ($warning ? '[WARN] ' : '[FAIL] ')).$message.PHP_EOL;
};
echo 'Preflight Ibnu Aqil — pemeriksaan CLI, tanpa menampilkan rahasia'.PHP_EOL;
$check(version_compare(PHP_VERSION, '8.3.0', '>='), 'PHP >= 8.3 (CLI aktif: '.PHP_VERSION.').');
foreach (['ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'mbstring', 'openssl', 'pcre', 'pdo', 'pdo_mysql', 'session', 'tokenizer', 'xml', 'gd'] as $extension) {
    $check(extension_loaded($extension), 'Ekstensi '.$extension.'.');
}
$check(function_exists('imagewebp'), 'GD mendukung pengodean WebP.');
$check(is_file($root.'/vendor/autoload.php'), 'Dependensi vendor tersedia.');
$check(is_file($root.'/.env'), 'Konfigurasi .env tersedia di akar aplikasi, di luar document root.');
foreach (['storage', 'storage/app/private', 'storage/framework', 'storage/logs', 'bootstrap/cache'] as $directory) {
    $path = $root.'/'.$directory;
    $canWrite = false;
    if (is_dir($path)) {
        $probe = $path.'/.preflight-'.bin2hex(random_bytes(8));
        $handle = @fopen($probe, 'x');
        if ($handle !== false) {
            $canWrite = fwrite($handle, 'ok') === 2;
            fclose($handle);
            $canWrite = @unlink($probe) && $canWrite;
        }
    }
    $check($canWrite, 'Direktori '.$directory.' ada dan dapat ditulis oleh CLI.');
}
$iniBytes = static function (string $value): int {
    $value = trim($value);
    if ($value === '-1') { return PHP_INT_MAX; }
    $power = match (strtolower(substr($value, -1))) { 'g' => 3, 'm' => 2, 'k' => 1, default => 0 };
    return (int) ((float) $value * (1024 ** $power));
};
$check($iniBytes((string) ini_get('memory_limit')) >= 256 * 1024 * 1024, 'memory_limit CLI disarankan minimal 256 MB untuk pengolahan foto.', true);
$check((bool) ini_get('file_uploads'), 'file_uploads aktif; periksa juga PHP web/cPanel.', true);

if (is_file($root.'/vendor/autoload.php') && is_file($root.'/.env')) {
    try {
        require $root.'/vendor/autoload.php';
        $app = require $root.'/bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $check(config('app.env') === 'production', 'APP_ENV=production.');
        $check(config('app.debug') === false, 'APP_DEBUG=false.');
        $check(filled(config('app.key')), 'APP_KEY sudah diisi; nilainya tidak ditampilkan.');
        $url = (string) config('app.url');
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $check(parse_url($url, PHP_URL_SCHEME) === 'https' && $host !== '' && ! in_array($host, ['localhost', '127.0.0.1', 'example.invalid'], true) && ! str_ends_with($host, '.invalid'), 'APP_URL menggunakan domain HTTPS yang sudah disiapkan.');
        $check(config('session.secure') === true, 'Cookie sesi hanya melalui HTTPS.');
        $check(config('session.encrypt') === true, 'Enkripsi sesi aktif.');
        $check(config('session.same_site') === 'lax', 'SameSite cookie sesi=lax.');
        $check(config('database.default') === 'mysql', 'Koneksi produksi memakai MySQL/MariaDB (driver mysql).');
        $check(config('queue.default') === 'sync', 'Antrean sync; worker permanen tidak diperlukan.');
        $check(config('mail.default') === 'smtp' && filled(config('mail.mailers.smtp.host')) && filled(config('mail.from.address')), 'SMTP dan pengirim sudah diisi; pengiriman nyata masih harus diuji.');
        $check(config('portal.controlled_video_enabled') === false, 'Video terkendali tetap nonaktif sampai integrasi penyedia tersedia.');
        $uploadKb = (int) config('portal.upload_max_kb');
        $check($uploadKb > 0 && $uploadKb <= 51200, 'Batas unggah aplikasi positif dan maksimal 50 MB per file.');
        $check($iniBytes((string) ini_get('upload_max_filesize')) >= $uploadKb * 1024, 'upload_max_filesize CLI memenuhi batas aplikasi; cocokkan dengan PHP web.', true);
        $check($iniBytes((string) ini_get('post_max_size')) > $uploadKb * 1024, 'post_max_size CLI lebih besar dari satu file; sesuaikan untuk banyak file.', true);
        $private = realpath(storage_path('app/private'));
        $public = realpath(public_path());
        $check($private !== false && $public !== false && ! str_starts_with(strtolower($private.DIRECTORY_SEPARATOR), strtolower($public.DIRECTORY_SEPARATOR)), 'File privat berada di luar direktori public aplikasi.');
        if (in_array('--database', $argv, true)) {
            Illuminate\Support\Facades\DB::connection()->getPdo();
            $check(true, 'Koneksi database berhasil.');
            foreach (['users', 'schools', 'activities', 'materials', 'assets', 'access_grants', 'content_pages', 'articles', 'settings', 'audit_logs', 'sessions', 'password_reset_tokens'] as $table) {
                $check(Illuminate\Support\Facades\Schema::hasTable($table), 'Tabel '.$table.' tersedia.');
            }
        } else {
            echo '[INFO] Tambahkan --database setelah migrasi untuk memeriksa koneksi dan tabel.'.PHP_EOL;
        }
    } catch (Throwable $exception) {
        $check(false, 'Bootstrap/database gagal. Periksa log privat/konfigurasi; detail rahasia tidak ditampilkan.');
    }
}
echo '[MANUAL] Pastikan document root domain adalah public, bukan akar aplikasi.'.PHP_EOL;
echo '[MANUAL] Cocokkan PHP web/CLI; uji HTTPS, email, upload, akses lintas sekolah, serta unduhan.'.PHP_EOL;
echo 'Hasil: '.$failed.' kegagalan, '.$warned.' peringatan. Ini bukan sertifikasi kesiapan produksi.'.PHP_EOL;
exit($failed === 0 ? 0 : 1);
