# Panduan pengelola Ibnu Aqil

Masuk melalui `/login`, lalu buka `/admin`. Gunakan akun individu; jangan membagikan satu akun kepada beberapa orang. Pendaftaran mandiri ditutup. Admin awal dibuat menggunakan perintah aman yang dijelaskan dalam panduan pemasangan.

## Peran dan sekolah

- **Admin lembaga** mengelola seluruh bagian, termasuk sekolah, pengguna, hak akses, halaman publik, dan kontak.
- **Pengelola materi** hanya mengelola kegiatan, materi, dan lampiran pengimbasan. Pembatasan juga diperiksa di server pada setiap rute, termasuk ketika URL diketik langsung.
- **Sekolah mitra** hanya mengakses kegiatan yang diizinkan untuk sekolahnya melalui portal.

Setiap pengguna harus dihubungkan dengan satu sekolah. Untuk akun internal, buat satu sekolah internal Ibnu Aqil. Satu sekolah dapat memiliki beberapa pengguna.

Di **Sekolah mitra**, tambah nama dan kode unik sekolah. Menonaktifkan sekolah menghentikan akses seluruh akun sekolah tersebut pada pemeriksaan akses berikutnya. Data tetap disimpan agar dapat diaktifkan kembali.

Di **Pengguna**, isi nama, email individu, sekolah, dan peran. Aplikasi membuat kata sandi acak yang tidak ditampilkan. Pilih pengiriman undangan agar pengguna mendapatkan tautan untuk membuat kata sandinya sendiri. Pengiriman memerlukan SMTP yang sudah dikonfigurasi. Jika belum terkirim, akun tetap tersimpan; perbaiki SMTP lalu tekan **Kirim tautan pengaturan kata sandi**. Pada lingkungan pengembangan dengan mailer log, email hanya dicatat secara lokal, tidak sampai ke kotak masuk. Jangan membagikan log email kepada pihak lain.

Admin tidak dapat menurunkan peran atau menonaktifkan akun dirinya sendiri melalui pengelolaan pengguna. Gunakan admin lain yang berwenang untuk tindakan tersebut. Nonaktifkan akun yang tidak digunakan.

## Kegiatan, materi, dan lampiran

1. Buat kegiatan dengan judul, tema, tahun, dan deskripsi. Simpan sebagai draf selama persiapan.
2. Tambahkan materi, pilih kegiatan dan jenis materi, isi pembuat serta deskripsinya. Simpan untuk membuka bagian lampiran.
3. Unggah PDF atau foto JPEG/PNG/WebP yang sudah dikompres. Maksimal 10 file sekali unggah; batas per file mengikuti `UPLOAD_MAX_KB`. Aplikasi memeriksa ukuran, ekstensi, dan MIME file di server, serta menyimpan file dengan nama aman di luar direktori publik.
4. Tentukan izin unduhan untuk setiap kelompok unggahan. Izin unduhan sekolah dan lampiran harus sama-sama aktif agar unduhan tersedia.
5. Terbitkan kegiatan serta materi setelah siap. Keduanya harus terbit agar mitra dapat melihatnya. Tombol **Tarik ke draf** menghilangkan materi/kegiatan dari portal tanpa menghapus data.

Lampiran dapat dihapus permanen dari halaman materi. Simpan salinan cadangan terlebih dahulu jika masih diperlukan. File yang masih digunakan sebagai sampul konten publik harus dilepas dari konten tersebut sebelum dihapus.

Untuk video, pilih YouTube atau Vimeo lalu masukkan ID, bukan URL/kode embed. Mode **tautan tersembunyi (unlisted)** tidak mencegah tautan dibagikan kembali. Mode **penyedia dengan kontrol akses** tetap draf sampai penyedia dan pemeriksaan akses benar-benar terpasang; sekadar mengubah konfigurasi tidak cukup untuk mengaktifkan layanan. Video tidak diunggah ke hosting 15 GB. Pembatasan tampilan/unduhan tidak menjamin materi bebas salin atau rekam.

## Memberi dan mencabut akses

Di **Hak akses sekolah**, pilih sekolah dan kegiatan. Satu pasangan sekolah/kegiatan mempunyai satu catatan; edit catatan tersebut untuk perubahan. Aktifkan izin, isi masa berlaku opsional, dan tentukan izin unduhan. Tanggal menggunakan zona waktu aplikasi.

Tombol **Cabut** menonaktifkan izin tanpa menghapus riwayat. Untuk membuka kembali, ubah catatan lalu centang **Hak akses aktif**. Sekolah, pengguna, izin, kegiatan, dan materi harus sama-sama aktif/terbit. Tautan file diperiksa kembali di server. File yang sudah diunduh sebelumnya tidak dapat ditarik dari perangkat penerima.

## Konten website publik

**Halaman & profil unit** memuat halaman lembaga, profil MI/SMP/SMA/SMK/pesantren/KBIHU, program, fasilitas, dan informasi pendaftaran. Edit halaman yang sudah disiapkan, pertahankan slugnya agar sesuai navigasi. Halaman tambahan dapat dibuka melalui `/halaman/SLUG`, dan profil unit melalui `/unit/SLUG`; bagikan tautan tersebut setelah konten terbit. Isi sebagai teks biasa, pisahkan paragraf dengan baris kosong. Catatan sumber hanya untuk admin.

**Berita, galeri & prestasi** memakai pilihan jenis masing-masing. Unggah foto asli yang mempunyai izin penggunaan di bagian bawah formulir, lalu pilih dalam **Foto sampul / foto galeri**. Setiap item galeri dapat menggunakan satu foto beserta keterangannya. Setelah unggah, pilihan foto tersedia saat halaman dimuat kembali.

Konten baru selalu dimulai sebagai draf. Sebelum menerbitkan halaman, berita, prestasi, atau galeri, centang verifikasi hanya setelah isi dan sumber telah dikonfirmasi. Brosur tahun 2025/2026 bukan bukti bahwa biaya, kontak, masa pendaftaran, dan klaim masih berlaku. Jangan menerbitkan statistik, testimoni, nama pengurus, legalitas, prestasi, atau akreditasi yang belum dipastikan. Jika mengubah informasi yang perlu dikonfirmasi ulang, tarik ke draf dan hapus tanda verifikasi sampai selesai.

Di **Kontak & pendaftaran**, simpan hanya email, telepon, alamat, WhatsApp, lokasi, dan tautan pendaftaran resmi yang sudah dikonfirmasi. Kosongkan yang belum diketahui. Semua tautan harus HTTPS; WhatsApp memakai `https://wa.me/NOMOR` atau `https://api.whatsapp.com/send?...`. Pengaturan ini langsung dapat digunakan website publik. Tautan pendaftaran mengarah ke sistem yang sudah ada.

**Foto tampilan website** (hero beranda, galeri "Suasana Ibnu Aqil", dan foto pada profil unit) dibaca dari `public/assets/foto/`, bukan dari unggahan admin. Nama berkas yang dikenali: `masjid-ibnu-aqil.jpg`, `manasik-kabah.jpg`, `ilustrasi-gerbang.jpg`, dan logo `logo-yhasb-hd.png`. Slot hanya tampil jika berkasnya ada; tanpa berkas, website memakai ilustrasi bawaan. Gunakan foto resmi yang sudah diizinkan (lebar minimal 1600 px, JPEG ≤ 400 KB), utamakan foto gedung/lingkungan, dan hindari wajah peserta didik tanpa persetujuan wali. Catat sumber setiap berkas di `docs/asset-provenance.json`.

## Pemantauan dan pemeliharaan

Ringkasan admin menunjukkan jumlah lampiran dan total ukuran yang tercatat pada database. Ini bukan seluruh penggunaan cPanel: jatah hosting 15 GB juga mencakup kode aplikasi, database, email, log, dan backup. Periksa pemakaian aktual di cPanel, hapus backup lama sesuai kebijakan, dan optimalkan foto/PDF sebelum unggah.

**Catatan aktivitas** tersedia hanya untuk admin. Gunakan riwayat untuk menelusuri perubahan dan akses. Jangan menerbitkan catatan, data akun, atau file privat ke halaman publik. Ikuti prosedur backup dan pemulihan utama untuk database dan direktori penyimpanan privat secara bersamaan.

Pembaca PDF tersedia langsung di portal dengan navigasi halaman, zoom, dan pas layar. PDF dengan fitur aktif, formulir, enkripsi, atau object stream terkompresi ditolak; ekspor ulang sebagai PDF standar tanpa fitur tersebut. Jangan mengunggah ebook langganan atau milik pihak lain tanpa izin distribusi.
