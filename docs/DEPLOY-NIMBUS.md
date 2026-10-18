# Pemasangan di DomaiNesia Nimbus Go

Satu aplikasi Laravel menangani website publik, Portal Pengimbasan, dan admin. Targetnya shared hosting cPanel 15 GB/RAM 1 GB. Tidak memerlukan Docker, akses root, Node produksi, Redis, worker antrean, atau proses server permanen. CSS/JavaScript sudah berupa aset langsung. CBT dan ujian massal tidak termasuk.

Domain `ibnuaqileducationcentre.com` hanya rencana; kepemilikan, ketersediaan, DNS, sertifikat, dan akun hosting belum dianggap tersedia. Panduan ini tidak melakukan publikasi atau perubahan DNS.

## 1. Konfirmasi runtime

Periksa **PHP web dan PHP CLI**, keduanya harus **8.3 atau lebih baru**. Dependensi produksi dikunci dengan platform PHP 8.3 agar tidak diam-diam memerlukan PHP 8.4/8.5. Jalankan `php -v`, `composer --version`, dan `composer check-platform-reqs --no-dev` memakai binary PHP yang sesuai. Jalur PHP CLI pada cPanel dapat berbeda; konfirmasi kepada DomaiNesia.

Ekstensi wajib: ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl, pcre, PDO, pdo_mysql, session, tokenizer, xml, serta **GD dengan WebP**. Produksi memakai MySQL/MariaDB melalui koneksi `mysql`; SQLite hanya lokal. Laravel menyebut dukungan MySQL 5.7+ atau MariaDB 10.3+; gunakan versi yang masih dipelihara hosting. Migrasi harus diuji pada database akun aktual. [Deployment Laravel 13](https://laravel.com/docs/13.x/deployment), [database Laravel 13](https://laravel.com/docs/13.x/database), diperiksa 18 Oktober 2026.

Contoh MultiPHP INI Editor: `memory_limit=256M`, `upload_max_filesize=12M`, `post_max_size=128M`, `max_file_uploads=10`, untuk batas aplikasi `UPLOAD_MAX_KB=12288`. Batas host/proxy dapat lebih kecil. Jangan menaikkan batas aplikasi di atas PHP web. RAM akun 1 GB tidak menjamin kapasitas banyak unggahan bersamaan; uji pemakaian nyata.

## 2. Folder dan document root

```text
/home/CPANEL_USER/ibnu-aqil/       akar aplikasi, tidak terbuka ke web
  .env                          rahasia produksi
  vendor/
  storage/app/private/          PDF/foto di luar public
  public/                       document root domain
    index.php
    .htaccess
    assets/
```

Di **Domains → Document Root**, arahkan domain ke `/home/CPANEL_USER/ibnu-aqil/public`. Jangan arahkan ke akar aplikasi. `.env`, vendor, storage, database, log, dan arsip tidak boleh dapat diambil melalui browser. Tidak perlu `storage:link`. Laravel mengarahkan permintaan ke `public/index.php`; akar proyek dapat membocorkan konfigurasi. [Konfigurasi server Laravel](https://laravel.com/docs/13.x/deployment#server-configuration).

Jika domain utama terkunci pada `public_html`, simpan aplikasi tetap di `/home/CPANEL_USER/ibnu-aqil`. Salin **hanya isi `public/`**, termasuk `.htaccess`, ke `public_html`. Gunakan `deploy/public_html-index.php.example` sebagai `public_html/index.php` dan ganti `CPANEL_USER`. Template memakai akar aplikasi di luar web serta menyesuaikan public path. Jangan menyalin `.env`, kode aplikasi, atau storage ke `public_html`. Saat pembaruan salin ulang aset publik, pertahankan index alternatif ini. Document root langsung ke `public/` lebih mudah dirawat.

## 3. Dependensi dan konfigurasi

Ekstrak paket di folder aplikasi. Jika paket runtime menyertakan vendor, tidak perlu mengunduh ulang, tetapi periksa platform. Jika hanya sumber:

```sh
cd /home/CPANEL_USER/ibnu-aqil
composer install --no-dev --prefer-dist --optimize-autoloader
composer check-platform-reqs --no-dev
cp .env.example .env
```

Jika memori Composer di hosting kurang, instal dependensi di komputer pengembangan lalu unggah vendor. Pertahankan `composer.lock`; jangan `composer update` langsung di produksi. Buat database/pengguna khusus melalui MySQL Database Wizard, berikan akses hanya pada database aplikasi. Nama DB/user biasanya memakai prefiks cPanel. Isi `.env` secara privat:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://DOMAIN-YANG-DISETUJUI
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ISI_DATABASE_CPANEL
DB_USERNAME=ISI_USER_CPANEL
DB_PASSWORD=ISI_RAHASIA
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_STORE=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=daily
LOG_DAILY_DAYS=14
LOG_LEVEL=warning
UPLOAD_MAX_KB=12288
STORAGE_BUDGET_MB=10240
```

Ini contoh, bukan kredensial siap pakai. Jangan commit `.env`. Gunakan password database unik. Daily log merotasi log aplikasi selama 14 hari; log cPanel/email diatur terpisah. Biarkan `SESSION_DOMAIN` tidak diisi agar cookie mengikuti host. Setelah mengganti APP_URL, hapus/bangun ulang cache dan uji tautan email.

Untuk **instalasi baru saja**:

```sh
php artisan key:generate --force
```

Cadangkan APP_KEY secara terenkripsi dan terpisah; jangan membuat kunci baru saat restore. Folder storage dan bootstrap/cache harus writable oleh PHP web/CLI. Pada owner cPanel yang sama mulai dari direktori 755/file 644, `.env` 600 atau 640 sesuai owner/group. Perbaiki owner/group bila gagal, jangan memakai 777. `.env` tetap di luar public root.

## 4. Database dan admin awal

```sh
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --force
php artisan app:create-admin admin@EMAIL-RESMI-ANDA
```

Perintah admin meminta nama dan kata sandi secara tersembunyi, minimal 12 karakter dengan huruf/angka. Gunakan password manager; kata sandi tidak menjadi argumen shell. Akun terhubung ke sekolah internal Ibnu Aqil. Seeder dasar menyiapkan konten aman; **jangan menjalankan DemoSeeder atau app:demo-access pada produksi**. Jangan `migrate:fresh` pada database berisi data.

Masuk, ubah kata sandi jika diminta, lalu ikuti [panduan admin](ADMIN.md). Konten dari brosur yang belum dikonfirmasi tetap draf.

## 5. Email, HTTPS, cache, cron

Isi MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS, serta MAIL_FROM_NAME dari mailbox resmi. Pilih TLS/port sesuai penyedia dan verifikasi SPF/DKIM lewat cPanel. SMTP diperlukan untuk undangan/reset; tidak ada layanan email berbayar baru. Email sinkron tidak memerlukan worker. Uji email diterima, URL benar dan HTTPS, token kedaluwarsa/sekali pakai. Mailer log hanya lokal dan tidak mengirim email.

Aktifkan sertifikat valid dan paksa HTTPS melalui cPanel. Jika ada reverse proxy host, pastikan protokol yang diterima Laravel benar. Cookie produksi memerlukan HTTPS.

```sh
php deploy/preflight.php
php deploy/preflight.php --database
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Preflight hanya CLI dan tidak menampilkan rahasia; memeriksa ekstensi, konfigurasi produksi, izin CLI, serta koneksi/tabel dengan `--database`. PHP web, SMTP nyata, document root aktual, dan izin web tetap dicek manual. Setelah perubahan `.env`, jalankan `config:clear` dan `config:cache`.

Cron cPanel berikut opsional untuk retensi audit; ganti binary PHP sesuai host:

```cron
* * * * * cd /home/CPANEL_USER/ibnu-aqil && /JALUR/PHP83 artisan schedule:run >> /dev/null 2>&1
```

Scheduler memangkas audit 90 hari. Akses kedaluwarsa ditolak per permintaan walaupun cron mati. Tidak perlu worker permanen. Periksa scheduler manual saat pemasangan sebelum menyembunyikan output rutinnya.

## 6. Uji penerimaan host aktual

Uji migrasi MySQL baru, halaman/unit, HP/keyboard, login/logout, nonaktif akun, undangan/reset email, peran, upload valid/berbahaya/berlebih, publikasi, grant/revoke/expiry, tampilan/unduhan, dan akses sekolah A ke materi/file sekolah B melalui URL langsung. Pastikan `/.env`, `/storage/...`, `/vendor/...`, serta SQL/ZIP tidak terbuka; sitemap tidak memuat area privat. Uji backup/restore sebelum data penting masuk.

Laporan pengujian paket menyebut hanya yang benar-benar dijalankan. SQLite lokal dan tangkapan layar tidak membuktikan kesiapan MySQL/cPanel/SMTP. Runtime, batas akun, domain, DNS, dan kredensial aktual harus dikonfirmasi sebelum peluncuran.

## 7. Ruang, video, pemindahan portal

STORAGE_BUDGET_MB=10240 membatasi media tercatat sekitar 10 GiB, menyisakan sekitar 5 GB dari target 15 GB untuk aplikasi, DB, email, log, dan ruang kerja. Satuan kuota host dapat berbeda; gunakan cPanel sebagai acuan. Batas ini bukan reservasi disk dan tidak menghitung file yatim/backup. Simpan backup terenkripsi **di luar hosting ini**.

Video tidak diunggah ke hosting. YouTube/Vimeo memerlukan akun/syarat masing-masing; yang aktif hanya embed ber-ID dengan mode link. Unlisted bukan kontrol akses kuat. Mode controlled tetap draf sampai penyedia dan pemeriksaan server tersedia. Tidak ada langganan video atau layanan berbayar baru yang diaktifkan.

Domain dapat diganti lewat APP_URL dan konfigurasi hosting. Portal ke subdomain memerlukan penyesuaian rute, cookie/sesi, HTTPS, serta URL reset; jangan menyalin file privat ke subdomain publik. Pertahankan otorisasi server dan database yang konsisten.
