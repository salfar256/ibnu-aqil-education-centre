# Website publik di GitHub Pages

GitHub Pages menayangkan HTML, CSS, JavaScript, dan gambar publik. Login, admin, database, pemeriksaan izin sekolah, dan materi privat tetap dijalankan oleh aplikasi PHP di Nimbus. Tidak ada formulir login demonstrasi pada versi statis. Saat alamat portal belum diatur, tombol portal membuka halaman status yang menjelaskan akses dan menyediakan WhatsApp resmi.

Target repositori: `salfar256/ibnu-aqil-education-centre`. Alamat project Pages: `https://salfar256.github.io/ibnu-aqil-education-centre/`. Ini tidak membeli, memindahkan, atau mengubah DNS domain utama yang direncanakan.

## Membuat ekspor

Siapkan aplikasi, database, dan konten yang sudah diverifikasi terlebih dahulu. Dari akar aplikasi:

```sh
php artisan app:export-public --url=https://salfar256.github.io/ibnu-aqil-education-centre --output=pages
```

Direktori output harus baru atau kosong dan berada langsung di akar aplikasi. Pilih nama lain, misalnya `--output=pages-baru`, jika ekspor sebelumnya masih ada. Perintah tidak menghapus direktori atau menimpa hasil yang sudah berisi berkas. Path absolut, traversal, symlink, dan direktori sumber seperti `public`, `storage`, atau `vendor` ditolak.

Ketika portal PHP sudah aktif melalui HTTPS, gunakan alamat sebenarnya:

```sh
php artisan app:export-public --url=https://salfar256.github.io/ibnu-aqil-education-centre --output=pages-baru --portal-url=https://portal.example.org/portal
```

`portal.example.org` pada contoh harus diganti; alamat ini tidak dikonfigurasi secara otomatis. Tautan portal pada situs publik tetap membuka halaman pengantar, lalu mengarahkan pengguna ke portal yang dikonfigurasi. Tidak ada akun, kata sandi, token, atau URL berkas privat di hasil ekspor.

Ekspor mengambil halaman dan artikel berstatus terbit yang memiliki waktu verifikasi, serta artikel yang tanggal terbitnya sudah tiba. Halaman, profil unit, artikel, galeri, prestasi, dan seluruh halaman pagination dirender melalui kernel HTTP lokal aplikasi. HTML tidak diambil dari server eksternal. Sampul hanya disalin jika aset berstatus publik dan digunakan artikel terverifikasi yang terbit. Aset tampilan yang dirujuk disalin dari `public/assets`; file PHP, `.env`, database, log, akun, hak akses, dan materi portal tidak disalin. Halaman kustom menggunakan `/halaman/{slug}/`.

URL ditulis dengan awalan project Pages dan diakhiri `/` untuk halaman direktori. Hasil berisi `index.html`, `404.html`, `.nojekyll`, `sitemap.xml`, `robots.txt`, aset tampilan, dan gambar sampul publik. Tautan ke profil atau informasi yang belum terbit dihilangkan dari HTML statis. Tidak ada `CNAME` yang dibuat.

## Penerbitan melalui GitHub Actions

Pada Settings → Pages repositori, pilih sumber **GitHub Actions**. Workflow `.github/workflows/pages.yml` berjalan setelah push ke `main` atau melalui **Run workflow**. Job pertama menjalankan seluruh tes aplikasi pada PHP 8.3 di Linux dengan dependensi framework asli. Job build menunggu tes lulus, kemudian menyiapkan PHP 8.3, menginstal dependensi produksi, membuat database SQLite sementara dari migrasi dan data publik yang disimpan di repositori, menjalankan ekspor, lalu mengunggah direktori `pages` saja. Deployment menggunakan environment `github-pages` dan izin `pages: write` serta `id-token: write` pada job deploy. Jangan mengunggah `.env` lokal atau database Nimbus ke repositori.

Database build tidak mengambil perubahan konten dari database produksi Nimbus. Setelah mengedit melalui admin Nimbus, ekspor ulang dari aplikasi dengan database yang sesuai lalu unggah hasilnya, atau perbarui sumber publik terverifikasi yang dipakai workflow. Berkas pengimbasan privat tidak boleh dipindahkan ke GitHub. Menarik konten langsung dari Nimbus memerlukan integrasi khusus yang belum disediakan.

Jika portal sudah aktif, atur repository variable `PORTAL_URL` pada Settings → Secrets and variables → Actions → Variables dengan URL HTTPS nyata portal. Workflow meneruskannya ke opsi `--portal-url`. Biarkan kosong selama portal belum aktif. URL bukan rahasia; jangan menyimpan token atau kredensial di parameter ini.

## Pemeriksaan sebelum dan sesudah terbit

- Jalankan `php artisan test` dan periksa halaman ekspor melalui server statis, termasuk navigasi HP, profil unit, pagination, gambar, canonical, serta tombol portal.
- Pastikan hanya konten terverifikasi yang muncul dan tidak ada akun atau materi sekolah pada artefak Pages.
- Periksa status workflow dan URL deployment yang dikembalikan GitHub. Keberhasilan ekspor lokal tidak membuktikan deployment GitHub selesai.
- Untuk memperbarui versi statis, jalankan ulang build setelah mengubah konten sumber; hasil sebelumnya tidak berubah otomatis ketika database Nimbus diedit.

Workflow menggunakan tag rilis resmi yang diperiksa pada 18 Oktober 2026: checkout `v7.0.1`, configure-pages `v6.0.0`, upload-pages-artifact `v5.0.0`, deploy-pages `v5.0.1`, dan setup-php `2.40.0`. Rujukan: [workflow Pages resmi](https://docs.github.com/en/pages/getting-started-with-github-pages/using-custom-workflows-with-github-pages), [checkout](https://github.com/actions/checkout/releases/tag/v7.0.1), [configure-pages](https://github.com/actions/configure-pages/releases/tag/v6.0.0), [upload-pages-artifact](https://github.com/actions/upload-pages-artifact/releases/tag/v5.0.0), [deploy-pages](https://github.com/actions/deploy-pages/releases/tag/v5.0.1), dan [setup-php](https://github.com/shivammathur/setup-php/releases/tag/2.40.0).
