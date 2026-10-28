<?php
namespace App\Services;
// Showcase content taken from the official Ibnu Aqil brochures in the Canva folder "Brosur" (see docs/asset-provenance.json).
// Photos only render when their file exists, so a removed photo never leaves a broken image.
class SchoolProfile {
 private const ACTIVITIES=[
  'pramuka-membaca'=>['Siswa Pramuka belajar bersama','Ekstrakurikuler'],
  'silat-gasmi'=>['Latihan Pencak Silat GASMI','Ekstrakurikuler'],
  'santriwati-kitab'=>['Santriwati Ibnu Aqil bersama kitab pelajaran','Pesantren'],
  'praktik-farmasi'=>['Praktik laboratorium jurusan Farmasi SMK','Kejuruan'],
  'tadarus-bersama'=>['Tadarus dan pembiasaan ibadah bersama di halaman sekolah','Pembiasaan'],
  'shalat-dhuha'=>['Shalat Dhuha berjamaah di depan Masjid Ibnu Aqil','Pembiasaan'],
  'santri-putra'=>['Santri putra SMA Ibnu Aqil Boarding School','Boarding'],
  'paskibra'=>['Pasukan pengibar bendera Ibnu Aqil','Ekstrakurikuler'],
  'lab-farmasi'=>['Mengenal sediaan obat di laboratorium farmasi','Kejuruan'],
  'pelepasan-siswa'=>['Momen apresiasi bagi siswa Ibnu Aqil','Kegiatan'],
  'juara-akuntansi'=>['Juara 3 Lomba Cerdas Cermat Akuntansi HIMA Akuntansi UIKA Bogor','Prestasi'],
 ];
 private const FACILITIES=[
  ['assets/foto/web/masjid-cahaya-ilahi.jpg','Masjid Cahaya Ilahi','Masjid tiga lantai dengan halaman luas untuk shalat berjamaah, kajian, dan kegiatan keagamaan.'],
  ['assets/foto/web/perpustakaan.jpg','Perpustakaan','Koleksi buku untuk mendukung literasi dan kegiatan belajar mandiri siswa.'],
  ['assets/foto/web/lab-komputer-praktik.jpg','Laboratorium komputer','Mendukung pembelajaran TIK dan pengembangan keterampilan teknologi.'],
  ['assets/foto/kegiatan/lab-farmasi.jpg','Laboratorium farmasi','Tempat praktik jurusan Farmasi SMK Ibnu Aqil.'],
  ['assets/foto/kegiatan/lapangan-semi-indoor.jpg','Lapangan semi indoor','Area olahraga beratap untuk latihan dan kegiatan siswa.'],
  ['assets/foto/kegiatan/lapangan-upacara.jpg','Lapangan olahraga dan upacara','Dua lapangan luas di area sekolah dan masjid untuk upacara, olahraga, dan aktivitas luar ruang.'],
  ['assets/foto/manasik-kabah.jpg','Pusat manasik','Replika Ka\'bah untuk praktik manasik haji dan umrah.'],
  ['assets/foto/kegiatan/gedung-sekolah.jpg','Gedung sekolah','Ruang kelas, perpustakaan, ruang bimbingan dan konseling, serta aula serbaguna.'],
  ['assets/foto/kegiatan/gerbang-sekolah.jpg','Kampus Laladon','Satu kawasan untuk MI, SMP, SMA, SMK, pesantren, dan asrama putra-putri.'],
 ];
 private const UNIT_PHOTOS=[
  'mi'=>['mi-seluruh-siswa','mi-market-day','mi-penyambutan','mi-belajar-lesehan','mi-market-day-mahkota','mi-guru-siswa','mi-belajar-koridor','mi-sanlat'],
  'smp'=>['pramuka-membaca','silat-panggung','math-club','science-club','pramuka','futsal'],
  'sma'=>['santri-putra','kelas-tik','juara-akuntansi','santriwati-kitab','perpustakaan','mc-santri'],
  'smk'=>['praktik-farmasi','lab-farmasi','paskibra','pelepasan-siswa'],
  'pesantren'=>['masjid-cahaya-ilahi','lalaran-targhib','santri-berkumpul','tadarus-bersama','santriwati-kitab','mc-santri'],
 ];
 // Photos supplied by the user on 22 October 2026 (Drive folder "Foto Untuk Web"), resized into public/assets/foto/web.
 private const WEB_PHOTOS=[
  'masjid-cahaya-ilahi'=>['Mengaji bersama di halaman Masjid Cahaya Ilahi','Pembiasaan'],
  'lalaran-targhib'=>['Lalaran santri pada Targhib Ramadhan','Pesantren'],
  'santri-berkumpul'=>['Santri berkumpul untuk kajian bersama','Pesantren'],
  'mc-santri'=>['Santri belajar tampil sebagai pembawa acara','Public speaking'],
  'kelas-tik'=>['Pembelajaran TIK di laboratorium komputer','Belajar'],
  'lab-komputer-praktik'=>['Praktik di laboratorium komputer','Belajar'],
  'perpustakaan'=>['Membaca di perpustakaan sekolah','Literasi'],
  'math-club'=>['Kegiatan Math Club','Klub belajar'],
  'math-club-2'=>['Math Club bersama guru pembimbing','Klub belajar'],
  'science-club'=>['Eksperimen bersama Science Club','Klub belajar'],
  'pramuka'=>['Latihan Pramuka','Ekstrakurikuler'],
  'pramuka-membaca-buku'=>['Anggota Pramuka belajar bersama','Ekstrakurikuler'],
  'silat-panggung'=>['Penampilan Pencak Silat','Ekstrakurikuler'],
  'silat-atraksi-api'=>['Atraksi Pencak Silat','Ekstrakurikuler'],
  'silat-jurus'=>['Peragaan jurus Pencak Silat','Ekstrakurikuler'],
  'karate'=>['Latihan Karate','Ekstrakurikuler'],
  'futsal'=>['Pertandingan futsal di lapangan semi indoor','Olahraga'],
  'futsal-latihan'=>['Latihan futsal','Olahraga'],
  'mi-seluruh-siswa'=>['Keluarga besar MI Ibnu Aqil','MI'],
  'mi-market-day'=>['Market Day MI Ibnu Aqil','MI'],
  'mi-market-day-mahkota'=>['Kreativitas siswa MI saat Market Day','MI'],
  'mi-penyambutan'=>['Penyambutan siswa baru MI','MI'],
  'mi-belajar-lesehan'=>['Siswa MI belajar bersama','MI'],
  'mi-belajar-koridor'=>['Belajar kelompok di koridor MI','MI'],
  'mi-kegiatan-bersama'=>['Kegiatan bersama siswa MI','MI'],
  'mi-foto-bersama'=>['Siswa dan guru MI Ibnu Aqil','MI'],
  'mi-guru-siswa'=>['Guru dan siswa MI di halaman sekolah','MI'],
  'mi-sanlat'=>['Pesantren kilat (Sanlat) MI Ibnu Aqil','MI'],
  'mi-guru'=>['Dewan guru MI Ibnu Aqil','MI'],
  'siswa-smp'=>['Siswa SMP Ibnu Aqil','SMP'],
  'siswa-sma'=>['Siswa dan siswi SMA Ibnu Aqil Boarding School','SMA'],
  'siswi-sma'=>['Siswi SMA Ibnu Aqil Boarding School','SMA'],
  'siswa-smk'=>['Siswa SMK Ibnu Aqil','SMK'],
  'siswi-smk'=>['Siswi SMK Ibnu Aqil jurusan Farmasi','SMK'],
 ];
 private const MARQUEE=[
  ['masjid-cahaya-ilahi','kelas-tik','silat-panggung','mi-market-day','science-club','lalaran-targhib','futsal','perpustakaan','mi-penyambutan','pramuka'],
  ['math-club','santri-berkumpul','karate','mi-seluruh-siswa','lab-komputer-praktik','silat-atraksi-api','mc-santri','futsal-latihan','mi-market-day-mahkota','math-club-2'],
 ];
 public const LEADERS=[
  ['ketua-yayasan','Hj. Saidah Hilmiyah, S.Pd., M.M.','Ketua Yayasan'],
  ['pembina-kh-agus-salim','KH. Agus Salim, S.Sos., M.M.','Pendiri & Pembina Yayasan'],
 ];
 public const STATS=[
  ['1992','Ibnu Aqil berdiri'],
  ['5','Unit pendidikan, dari MI sampai SMK dan pesantren'],
  ['A','Akreditasi SMP, SMA, dan SMK'],
  ['30+','Kitab kuning dipelajari di pesantren'],
  ['25+','Pilihan ekstrakurikuler'],
 ];
 public const HIGHLIGHTS=[
  ['masjid-cahaya-ilahi','Ibadah yang dibiasakan','Shalat Dhuha berjamaah, tadarus Al-Qur\'an, bimbingan BTQ metode Tilawati, dan Majlis Bina Ruhiah menjadi bagian dari hari-hari siswa.'],
  ['lalaran-targhib','Tradisi kitab kuning dan tahfidz','Santri mempelajari lebih dari 30 kitab kuning dengan metode Amsilati, bandongan, sorogan, dan klasikal, serta program tahfidz Al-Qur\'an dan hadis.'],
  ['santri-berkumpul','Bisa sambil mondok','Siswa SMP dan SMA boleh memilih tinggal di asrama pesantren untuk membina kemandirian, adab, dan kepemimpinan. Sifatnya pilihan, tidak wajib.'],
  ['siswi-smk','Siap kuliah dan berkarya','SMK membuka jurusan Farmasi, Akuntansi dan Keuangan Lembaga, serta Bisnis dan Pemasaran, dengan PKL, kunjungan industri, dan teaching factory.'],
  ['silat-panggung','Bakat yang diasah','Lebih dari 25 ekstrakurikuler, dari Pencak Silat GASMI, Paskibra, dan Pramuka sampai tilawah, kaligrafi, hadroh, dan English Club.'],
  ['lab-komputer-praktik','Lingkungan yang lengkap','Masjid tiga lantai, laboratorium komputer, sains, dan farmasi, lapangan luas, aula, auditorium, serta pusat manasik.'],
 ];
 public const ACHIEVEMENTS=[
  ['Juara 3 Lomba Cerdas Cermat Akuntansi','Himpunan Mahasiswa Akuntansi UIKA Bogor','Siswa Ibnu Aqil'],
  ['Juara 1, 2, dan 3 Pencak Silat Championship','Se-Jabodetabek','2021/2022'],
  ['Juara 1 Pencak Silat tingkat Nasional','Nasional','2021/2022'],
  ['Juara 1 Muaythai tingkat Nasional','Mewakili Jawa Barat','2021/2022'],
  ['Juara 3 Pencak Silat Tunggal Putri','Se-Kabupaten Bogor','2018/2019'],
  ['Juara 3 Qori\'ah','Se-Kabupaten Bogor','2017/2018'],
  ['Juara 1 Tilawah','Se-Kabupaten Bogor','2016/2017'],
  ['Juara 3 Olimpiade Akuntansi tingkat SMK','Se-Jabodetabek','2015/2016'],
 ];
 public const ADMISSION_STEPS=[
  ['Isi formulir','Mengisi formulir pendaftaran dan membayar biaya pendaftaran, langsung di kantor pendaftaran atau melalui admin PPDB.'],
  ['Lengkapi dokumen','Menyerahkan formulir beserta dokumen persyaratan peserta didik baru.'],
  ['Tes dan wawancara','Mengikuti tes seleksi dan wawancara sesuai jadwal dari panitia PPDB.'],
  ['Daftar ulang','Setelah dinyatakan diterima, menyelesaikan biaya administrasi yang telah ditetapkan.'],
 ];
 public const SCHEDULE=[
  ['Layanan pendaftaran','Setiap hari kerja, pukul 08.00–15.00 WIB'],
  ['Jam belajar MI','Senin–Jumat, 07.25–14.00 WIB'],
  ['Jam belajar SMP, SMA, SMK','Senin–Jumat, 07.15–14.20 WIB'],
 ];
 public static function photo(string $key):?array{if(isset(self::ACTIVITIES[$key])){$file='assets/foto/kegiatan/'.$key.'.jpg';[$caption,$tag]=self::ACTIVITIES[$key];}elseif(isset(self::WEB_PHOTOS[$key])){$file=is_file(public_path('assets/foto/web/'.$key.'.webp'))?'assets/foto/web/'.$key.'.webp':'assets/foto/web/'.$key.'.jpg';[$caption,$tag]=self::WEB_PHOTOS[$key];}else return null;$path=public_path($file);if(!is_file($path)||is_link($path)||!($size=@getimagesize($path)))return null;return ['key'=>$key,'src'=>asset($file),'alt'=>$caption,'caption'=>$caption,'tag'=>$tag,'width'=>$size[0],'height'=>$size[1]];}
 public static function activities(?int $limit=null):array{$photos=array_values(array_filter(array_map(fn($k)=>self::photo($k),array_keys(self::ACTIVITIES))));return $limit?array_slice($photos,0,$limit):$photos;}
 public static function marquee():array{return array_map(fn($row)=>array_values(array_filter(array_map(fn($k)=>self::photo($k),$row))),self::MARQUEE);}
 public static function unitPhotos(string $slug):array{return array_values(array_filter(array_map(fn($k)=>self::photo($k),self::UNIT_PHOTOS[$slug]??[])));}
 public static function facilities():array{$items=[];foreach(self::FACILITIES as [$file,$title,$text]){$path=public_path($file);if(is_file($path)&&!is_link($path)&&($size=@getimagesize($path)))$items[]=['src'=>asset($file),'title'=>$title,'text'=>$text,'width'=>$size[0],'height'=>$size[1]];}return $items;}
 public static function highlights():array{return array_map(fn($h)=>['photo'=>self::photo($h[0]),'title'=>$h[1],'text'=>$h[2]],self::HIGHLIGHTS);}
 public static function founder():?array{$file=is_file(public_path('assets/foto/web/kh-agus-salim.jpg'))?'assets/foto/web/kh-agus-salim.jpg':'assets/foto/kegiatan/pendiri-kh-agus-salim.jpg';$path=public_path($file);if(!is_file($path)||is_link($path)||!($size=@getimagesize($path)))return null;return ['src'=>asset($file),'width'=>$size[0],'height'=>$size[1]];}
 // Foundation leaders shown on the home hero; only portraits that exist on disk are returned.
 public static function leaders():array{$out=[];foreach(self::LEADERS as [$file,$name,$role]){$rel='assets/foto/web/'.$file.'.webp';$path=public_path($rel);if(is_file($path)&&!is_link($path)&&($size=@getimagesize($path)))$out[]=['src'=>asset($rel),'name'=>$name,'role'=>$role,'width'=>$size[0],'height'=>$size[1]];}return $out;}
}
