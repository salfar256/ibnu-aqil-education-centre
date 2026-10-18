# Arsitektur dan batas sistem

Satu aplikasi PHP/Laravel dengan Blade, CSS/JavaScript ringan, dan MySQL/MariaDB produksi. SQLite digunakan lokal. Tidak memakai Supabase, API AI, CBT, worker permanen, atau build frontend di shared hosting.

## Alur permintaan

```text
Browser HTTPS → public/index.php
  ├─ Publik: profil, halaman, berita, galeri, prestasi, kontak
  ├─ Auth: login/logout, perubahan dan pemulihan kata sandi
  ├─ Portal: login + akun/sekolah aktif + izin sekolah/kegiatan
  │    └─ File: pemeriksaan ulang izin → stream storage/app/private
  └─ Admin: login + akun aktif + peran
       ├─ admin: sekolah, pengguna, izin, CMS, kontak, audit
       └─ editor: kegiatan, materi, lampiran pengimbasan
```

Sesi memakai database/enkripsi, cookie produksi Secure/SameSite=Lax. Login/reset dibatasi percobaan. Formulir memakai CSRF; input divalidasi dan teks Blade di-escape. Hash kata sandi mengikuti framework, tanpa kata sandi bawaan tetap. Pendaftaran mandiri tidak tersedia.

## Data utama

| Entitas | Fungsi/hubungan |
|---|---|
| School | Beberapa akun per sekolah, status aktif. |
| User | Satu sekolah, peran admin/editor/partner, status dan kewajiban ganti password. |
| Activity | Kegiatan, tema, tahun, status; mempunyai materi. |
| Material | Satu kegiatan, deskripsi, pembuat, tanggal, jenis PDF/ebook/modul/foto/video. |
| Asset | Path acak, MIME, ukuran, unduhan, visibilitas; hubungan materi opsional. |
| AccessGrant | Pasangan sekolah/kegiatan unik, masa berlaku opsional, izin unduh. |
| ContentPage | Profil unit/halaman, ringkasan, isi, status, verifikasi, catatan sumber privat. |
| Article | Berita/galeri/prestasi, tanggal, verifikasi, sampul opsional. |
| Setting | Kontak dan tautan resmi yang dikonfirmasi. |
| AuditLog | Akses/unduhan/perubahan, pengguna/sekolah/objek/waktu, HMAC alamat IP. |

Migrasi `database/migrations/` menjadi acuan skema. Seeder dasar tidak menyediakan kata sandi tetap atau menganggap klaim belum terverifikasi sebagai informasi resmi. Demo dibatasi lingkungan local/testing dan tidak dibawa ke produksi.

## Kebijakan akses

Akun mitra dan sekolahnya harus aktif. Izin diberikan per **sekolah/kegiatan**, berlaku untuk seluruh akun sekolah itu. Grant harus aktif, belum kedaluwarsa, serta kegiatan dan materi harus terbit. Daftar, detail, pembaca file, dan unduhan menjalankan pemeriksaan server. Manipulasi ID/URL tidak melewati kebijakan; objek tanpa izin tidak ditampilkan.

Unduhan memerlukan izin pada grant **dan** asset. Menyembunyikan tombol bukan otorisasi. Mode hanya tampil tidak menjamin pencegahan salin, tangkapan layar, atau rekam. Revoke berlaku pada permintaan berikutnya dan tidak menghapus file yang sudah diterima.

Editor hanya mengelola pengimbasan; endpoint sekolah, akun, hak akses, CMS, kontak, dan audit menolak editor di server. Editor tidak dapat menaikkan perannya sendiri. Penonaktifan/tarik ke draf mempertahankan data; hapus lampiran adalah tindakan permanen terpisah.

## Berkas dan video

Seluruh PDF/foto berada di storage/app/private. Berkas privat melalui pemeriksaan portal; foto publik hanya dilayani sebagai sampul konten terbit/terverifikasi. Nama asli hanya metadata; nama path acak aman. Unggahan diperiksa ukuran, ekstensi, MIME, isi PDF, serta dimensi foto. Foto diperkecil/dikode ulang ke WebP. PDF dengan fitur aktif yang terdeteksi ditolak; sumber tetap harus tepercaya. Tidak ada antivirus eksternal yang diaktifkan.

Anggaran media awal 10 GiB dihitung dari metadata; kuota 15 GB juga mencakup kode, DB, email, log, dan backup sementara. Ini bukan reservasi disk dan tidak menghitung file yatim. Pantau cPanel dan simpan backup di luar hosting; pemulihan harus menjaga konsistensi DB/file.

Video menggunakan ID YouTube/Vimeo tervalidasi; URL embed dibentuk server tanpa kredensial frontend. Mode unlisted dapat dibagikan ulang. Controlled belum mempunyai penyedia aktif dan tidak dapat terbit. Aktivasi memerlukan integrasi token/pemeriksaan penyedia nyata dan pengujian; boolean konfigurasi saja bukan kontrol akses.

## Publik, SEO, operasi

Halaman/artikel membutuhkan status serta verifikasi; artikel memakai tanggal terbit. Kontak hanya disimpan setelah konfirmasi admin. Brosur lama tidak otomatis menjadi data terbaru. Konten belum terkonfirmasi tetap draf. Judul/deskripsi/canonical berbasis URL aplikasi. Sitemap hanya publik, robots melarang area privat, serta area privat mengirim noindex. Robots bukan perlindungan akses; server tetap memeriksa izin.

PHP web berjalan di cPanel, queue sync, cache file, sesi DB. Scheduler opsional memangkas audit 90 hari; expiry diperiksa per request. SMTP resmi diperlukan untuk undangan/reset. Platform video punya syarat/biaya akun masing-masing; tidak ada langganan baru diaktifkan.

APP_URL memungkinkan perubahan domain. Portal ke subdomain memerlukan pengaturan rute, cookie, HTTPS, dan tautan email dengan otorisasi yang sama. Lihat [deployment](DEPLOY-NIMBUS.md), [admin](ADMIN.md), dan [backup](BACKUP-RESTORE.md).

Pengujian lokal tidak mewakili beban nyata, SMTP, migrasi MySQL, atau batas akun Nimbus; host aktual masih perlu uji penerimaan. Laporan hanya mencatat pengujian yang dijalankan.

PDF ditampilkan melalui PDF.js 6.4.299 yang dihosting sendiri, berlisensi Apache-2.0. Worker, CMap, font standar, dan WASM disertakan. Setiap permintaan PDF tetap melewati route berkas dan pemeriksaan hak akses sekolah; PDF tidak menjadi aset publik. File dengan fitur aktif, formulir, enkripsi, atau object stream ditolak secara konservatif; ekspor ulang sebagai PDF biasa bila ditolak. Ini penyaringan defensif, bukan antivirus atau jaminan keamanan semua konten PDF.
