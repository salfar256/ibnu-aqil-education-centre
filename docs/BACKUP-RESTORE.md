# Backup dan pemulihan

Backup mencakup database **dan** lampiran privat dari titik waktu yang konsisten. Menyalin public saja tidak cukup. Simpan salinan terenkripsi di media/lokasi luar hosting milik lembaga. Aplikasi tidak menambahkan layanan backup berbayar.

## Yang dicadangkan

- Dump MySQL/MariaDB lengkap: sekolah, akun, hak akses, CMS, metadata lampiran, audit.
- Seluruh `storage/app/private/`, termasuk struktur path dan nama file.
- Kode/aset serta composer.lock dari versi yang sama. Vendor dapat ikut untuk restore tanpa Composer.
- `.env`, terutama **APP_KEY**, konfigurasi database/email, disimpan **terenkripsi dan terpisah**. Jangan memasukkan rahasia ke nama file, log, atau argumen shell.
- Catatan document root, versi PHP, cron, owner/group, dan izin folder. Cache/compiled view tidak wajib dicadangkan.

Backup memuat data pribadi dan materi berizin; batasi pemegang kunci. Contoh kebijakan untuk disepakati: harian 7 salinan, mingguan 4, bulanan 3. Jatah hosting 15 GB bukan tempat menampung beberapa backup media 10 GB. Jangan menyimpan SQL/ZIP di public root.

## Mengambil snapshot konsisten

Jadwalkan masa pemeliharaan dan beri tahu pengelola. Hentikan perubahan/unggahan selama snapshot; jeda cron pemangkasan jika ingin audit pada waktu yang sama. Pastikan ruang sementara memadai. Gunakan snapshot cPanel jika menjamin konsistensi database+file, atau terminal akun:

```sh
cd /home/CPANEL_USER/ibnu-aqil
php artisan down --retry=60
```

Ekspor struktur+data melalui phpMyAdmin (Custom/SQL), atau contoh terminal berikut. Direktori tujuan harus privat di luar document root dan sudah dibuat. `-p` meminta password secara interaktif:

```sh
mysqldump --single-transaction --quick --no-tablespaces --default-character-set=utf8mb4 -u CPANEL_DBUSER -p CPANEL_DATABASE > /LOKASI_PRIVAT/database.sql
tar -czf /LOKASI_PRIVAT/private-assets.tar.gz -C /home/CPANEL_USER/ibnu-aqil/storage/app private
```

Jika host membatasi mysqldump, gunakan ekspor cPanel/phpMyAdmin. Jangan memakai `-pKATASANDI`. Simpan versi sumber serta .env terenkripsi terpisah. Catat waktu/zona Asia/Jakarta, versi paket/commit, ukuran, dan SHA-256 (`sha256sum`). Pastikan dump tidak kosong/arsip dapat dibaca, pindahkan ke luar hosting, dan hapus salinan sementara sesuai kebijakan. Buka kembali situs dan cron:

```sh
php artisan up
```

Jika backup gagal, pastikan situs tidak tertinggal dalam pemeliharaan tanpa diketahui. Berkas ZIP yang tercipta saja belum membuktikan backup valid.

## Pemulihan

1. Pilih pasangan SQL dan lampiran dari snapshot yang sama; periksa checksum. Mulai di staging terisolasi dengan email keluar dimatikan bila memungkinkan. Cadangkan instalasi yang akan diganti.
2. Pasang versi sumber/aset yang cocok di luar public root, arahkan domain ke public/, dan sediakan runtime yang sama. Jika perlu jalankan `composer install --no-dev --prefer-dist --optimize-autoloader`.
3. Buat database baru dan pengguna khusus cPanel. Impor SQL melalui phpMyAdmin atau `mysql -u CPANEL_DBUSER -p CPANEL_DATABASE < /LOKASI_PRIVAT/database.sql`. Periksa nama database sebelum impor.
4. Kembalikan folder private ke `storage/app/private/` dengan path persis sesuai metadata. Jangan menaruhnya di public/public_html.
5. Pulihkan .env, **pertahankan APP_KEY lama**, sesuaikan host database/domain/mailbox. Jangan `key:generate`. Sesi lama boleh dihapus agar pengguna login kembali; ini tidak menghapus akun/hak akses.
6. Pastikan owner/group benar, storage dan bootstrap/cache writable, .env tidak terbuka ke web, serta HTTPS valid.
7. Jalankan `php artisan optimize:clear`, `php artisan migrate:status`, dan `php deploy/preflight.php --database`. Bila naik versi, periksa perubahan lalu jalankan hanya migrasi yang diperlukan setelah backup. Jangan `migrate:fresh` atau reseed demo. Bangun ulang config:cache, route:cache, dan view:cache.
8. Uji halaman, admin/mitra, dua sekolah terpisah, lampiran, unduhan, expiry, formulir, serta email reset. Bandingkan jumlah/ukuran file dan metadata snapshot. Aktifkan cron, kemudian buka kembali situs.

Kehilangan APP_KEY dapat membuat data/sesi terenkripsi tidak terbaca. Rotasi password database/SMTP saat insiden tidak menggantikan kebutuhan menyimpan APP_KEY. Batasi distribusi backup dan tangani indikasi kebocoran berdasarkan bukti.

## Latihan pemulihan

Uji setelah pemasangan awal, setelah perubahan besar, dan berkala sesuai kebijakan. Catat waktu, versi, jumlah file, serta hasil pengujian izin. Pengujian lokal belum membuktikan backup cPanel dan restore MySQL pada akun produksi berhasil.
