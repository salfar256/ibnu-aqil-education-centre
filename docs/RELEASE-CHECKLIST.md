# Pemeriksaan paket rilis

Pembuatan paket dilakukan setelah seluruh perubahan dan pengujian selesai. Direktori aplikasi kerja dapat berisi database, akun, dan lampiran untuk pratinjau; direktori tersebut **tidak boleh langsung di-ZIP atau diunggah ke GitHub**.

## Membentuk rilis bersih

Skrip pengembangan `work/package_release.py` memilih sumber `work/ibnu-aqil`, membentuk direktori dan ZIP dalam outputs. Ia tidak menghapus/menimpa tujuan yang sudah ada. Jika perlu rilis ulang, simpan versi sebelumnya di tempat yang dimaksud atau pilih workspace rilis baru; jangan menghapus direktori kerja secara membabi buta.

```sh
python work/package_release.py --package source --dry-run
python work/package_release.py --dry-run
python work/package_release.py
```

Jalankan perintah dari akar workspace. Pilihan `--package source` hanya membentuk sumber; `--package nimbus` hanya runtime. `--stage-only` membentuk direktori/manifest tanpa ZIP dan tetap menolak tujuan yang sudah ada. Jangan memakainya sebelum pekerjaan selesai jika kemudian ingin menghasilkan ZIP dengan pemanggilan default; buat ZIP dari hasil staging yang sudah diverifikasi atau gunakan rilis baru.

Runtime Nimbus mengambil dependensi hanya dari `work/production-vendor/vendor`, hasil `composer install --no-dev --prefer-dist --optimize-autoloader` terpisah. Metadata Composer harus `dev=false` dan tidak mengandung PHPUnit/Faker/Mockery/Collision/Boost. `vendor` pengembangan dalam aplikasi kerja tidak pernah disalin ke paket.

## Isi dan pengecualian

- Sumber: kode, aset, migrasi, seeder yang dijaga lingkungan, tests, dokumen, deploy, .github, composer.lock, dan .env.example kosong.
- Nimbus: sumber operasional serta vendor produksi, tanpa tests/phpunit.xml/.github/factory pengembangan. DemoSeeder tetap memiliki penjagaan local/testing dan tidak dipanggil saat pemasangan produksi.
- Keduanya menghilangkan .env nyata dan semua variannya, SQLite, sesi, upload privat/publik lokal, log, compiled views, cache bootstrap, alat runtime, Git metadata, node_modules, ekspor pratinjau, serta berkas frontend starter yang tidak dipakai.
- Folder runtime dibentuk kembali dengan .gitignore; aplikasi memperoleh direktori kosong yang benar. Tidak ada data demo akun/lampiran yang disalin.
- Symlink yang dapat membawa path luar ditolak. Pemindaian pola kunci/token tidak mencetak nilai rahasia; .env.example harus mempunyai APP_KEY serta kredensial DB/SMTP kosong. Pemindaian bukan pengganti review manusia.

Setiap direktori/ZIP memuat `RELEASE-MANIFEST.json` dengan path, ukuran, SHA-256, jumlah file dan total byte. Manifest tidak menghitung dirinya sendiri. Berkas ZIP mempunyai checksum .zip.sha256 tersendiri dan diuji CRC oleh skrip. Periksa jumlah/ukuran, daftar path, serta checksum sebelum unggah.

## Setelah paket dibuat

1. Ekstrak ZIP ke direktori baru dan pastikan tidak ada `.env` nyata, database SQLite, log, sesi, private uploads, kata sandi tetap aplikasi, atau token.
2. Pastikan source berisi migrasi/test/docs dan Nimbus berisi vendor produksi yang kompatibel PHP 8.3. Jalankan Composer check-platform-reqs pada runtime target.
3. Buat .env baru dari contoh, database baru, APP_KEY baru untuk instalasi baru, migrasi/seed dasar, serta admin interaktif. Uji sesuai panduan pemasangan.
4. Salin hanya sumber bersih ke repositori. GitHub Pages hanya boleh berisi ekspor website publik; jangan menyalin ZIP Nimbus, .env, portal, admin, berkas privat, atau formulir autentikasi ke Pages.
5. Pastikan laporan uji sesuai eksekusi nyata, sumber/foto mempunyai hak penggunaan, konten belum terkonfirmasi tetap draf, dan kesiapan produksi tidak diklaim sebelum uji MySQL/cPanel/SMTP serta restore aktual.

Skrip hanya membentuk paket lokal. Publikasi GitHub, Pages, hosting, atau perubahan DNS merupakan tindakan terpisah sesuai instruksi pemilik.
