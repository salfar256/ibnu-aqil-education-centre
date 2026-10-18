# Ibnu Aqil Education Centre

Website publik, Portal Pengimbasan sekolah mitra, dan panel admin dalam satu aplikasi Laravel 13/PHP 8.3+. Target produksi: MySQL/MariaDB pada cPanel Nimbus Go, document root `public/`. Tanpa Docker, Node produksi, atau worker permanen. CBT tidak termasuk.

## Jalankan lokal

Gunakan PHP 8.3+, ekstensi Laravel, PDO SQLite untuk lokal, dan GD/WebP untuk foto:

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Ubah `.env` lokal: `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://127.0.0.1:8000`, `DB_CONNECTION=sqlite`, `DB_DATABASE=/PATH/ABSOLUT/database/database.sqlite`, `SESSION_SECURE_COOKIE=false`, dan `MAIL_MAILER=log`. Buat file SQLite kosong pada path tersebut, kemudian:

```sh
php artisan migrate
php artisan db:seed
php artisan app:create-admin EMAIL-ADMIN-ANDA
php artisan serve --host=127.0.0.1 --port=8000
```

Admin meminta password melalui prompt tersembunyi; tidak ada kredensial tetap. Jangan membawa .env, log email, database, atau akun demo lokal ke produksi. Aset CSS/JavaScript langsung, tanpa build frontend.

## Pengujian dan pemasangan

```sh
php artisan test
php artisan view:cache
php artisan route:list
```

Test memakai konfigurasi pengujian terpisah. Lihat laporan pengujian dalam paket untuk hasil aktual dan keterbatasan. Pada host produksi gunakan `php deploy/preflight.php --database` sesudah konfigurasi/migrasi; pemeriksaan produksi dapat sengaja gagal pada lingkungan lokal.

- [Pemasangan Nimbus/cPanel](docs/DEPLOY-NIMBUS.md)
- [Panduan admin](docs/ADMIN.md)
- [Backup dan pemulihan](docs/BACKUP-RESTORE.md)
- [Arsitektur dan kebijakan akses](docs/ARCHITECTURE.md)

Domain rencana `ibnuaqileducationcentre.com` belum dianggap dimiliki/tersedia. Konten belum diverifikasi tetap draf. Video controlled belum aktif; unlisted bukan proteksi kuat. Runtime cPanel, MySQL, SMTP nyata, serta restore backup harus diuji sebelum peluncuran.
