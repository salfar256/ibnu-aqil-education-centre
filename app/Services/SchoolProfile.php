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
  ['assets/foto/masjid-ibnu-aqil.jpg','Masjid tiga lantai','Ruang ibadah yang luas dan nyaman untuk shalat berjamaah, kajian, dan kegiatan keagamaan.'],
  ['assets/foto/kegiatan/lab-komputer.jpg','Laboratorium komputer','Mendukung pembelajaran TIK dan pengembangan keterampilan teknologi.'],
  ['assets/foto/kegiatan/lab-farmasi.jpg','Laboratorium farmasi','Tempat praktik jurusan Farmasi SMK Ibnu Aqil.'],
  ['assets/foto/kegiatan/lapangan-semi-indoor.jpg','Lapangan semi indoor','Area olahraga beratap untuk latihan dan kegiatan siswa.'],
  ['assets/foto/kegiatan/lapangan-upacara.jpg','Lapangan olahraga dan upacara','Dua lapangan luas di area sekolah dan masjid untuk upacara, olahraga, dan aktivitas luar ruang.'],
  ['assets/foto/manasik-kabah.jpg','Pusat manasik','Replika Ka\'bah untuk praktik manasik haji dan umrah.'],
  ['assets/foto/kegiatan/gedung-sekolah.jpg','Gedung sekolah','Ruang kelas, perpustakaan, ruang bimbingan dan konseling, serta aula serbaguna.'],
  ['assets/foto/kegiatan/gerbang-sekolah.jpg','Kampus Laladon','Satu kawasan untuk MI, SMP, SMA, SMK, pesantren, dan asrama putra-putri.'],
 ];
 private const UNIT_PHOTOS=[
  'mi'=>['shalat-dhuha','tadarus-bersama','pramuka-membaca'],
  'smp'=>['pramuka-membaca','silat-gasmi','shalat-dhuha'],
  'sma'=>['santri-putra','juara-akuntansi','santriwati-kitab'],
  'smk'=>['praktik-farmasi','lab-farmasi','paskibra','pelepasan-siswa'],
  'pesantren'=>['tadarus-bersama','santriwati-kitab','shalat-dhuha'],
 ];
 public const STATS=[
  ['1994','Pondok Pesantren Ibnu Aqil berdiri'],
  ['6','Unit pendidikan dan layanan, dari MI sampai KBIHU'],
  ['A','Akreditasi SMP, SMA, dan SMK'],
  ['31','Kitab kuning dipelajari di pesantren'],
  ['25+','Pilihan ekstrakurikuler'],
 ];
 public const HIGHLIGHTS=[
  ['shalat-dhuha','Ibadah yang dibiasakan','Shalat Dhuha berjamaah, tadarus Al-Qur\'an, bimbingan BTQ metode Tilawati, dan Majlis Bina Ruhiah menjadi bagian dari hari-hari siswa.'],
  ['santriwati-kitab','Tradisi kitab kuning dan tahfidz','Santri mempelajari 31 kitab kuning dengan metode Amsilati, bandongan, sorogan, dan klasikal, serta program tahfidz Al-Qur\'an dan hadis.'],
  ['santri-putra','Asrama putra dan putri','SMA Ibnu Aqil Boarding School dan pondok pesantren membina kemandirian, adab, dan kepemimpinan dalam lingkungan asrama.'],
  ['praktik-farmasi','Siap kuliah dan berkarya','SMK membuka jurusan Farmasi, Akuntansi dan Keuangan Lembaga, serta Bisnis dan Pemasaran, dengan PKL, kunjungan industri, dan teaching factory.'],
  ['silat-gasmi','Bakat yang diasah','Lebih dari 25 ekstrakurikuler, dari Pencak Silat GASMI, Paskibra, dan Pramuka sampai tilawah, kaligrafi, hadroh, dan English Club.'],
  ['tadarus-bersama','Lingkungan yang lengkap','Masjid tiga lantai, laboratorium komputer, sains, dan farmasi, lapangan luas, aula, auditorium, serta pusat manasik.'],
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
 public static function photo(string $key):?array{if(!isset(self::ACTIVITIES[$key]))return null;$file='assets/foto/kegiatan/'.$key.'.jpg';$path=public_path($file);if(!is_file($path)||is_link($path)||!($size=@getimagesize($path)))return null;[$caption,$tag]=self::ACTIVITIES[$key];return ['key'=>$key,'src'=>asset($file),'alt'=>$caption,'caption'=>$caption,'tag'=>$tag,'width'=>$size[0],'height'=>$size[1]];}
 public static function activities(?int $limit=null):array{$photos=array_values(array_filter(array_map(fn($k)=>self::photo($k),array_keys(self::ACTIVITIES))));return $limit?array_slice($photos,0,$limit):$photos;}
 public static function unitPhotos(string $slug):array{return array_values(array_filter(array_map(fn($k)=>self::photo($k),self::UNIT_PHOTOS[$slug]??[])));}
 public static function facilities():array{$items=[];foreach(self::FACILITIES as [$file,$title,$text]){$path=public_path($file);if(is_file($path)&&!is_link($path)&&($size=@getimagesize($path)))$items[]=['src'=>asset($file),'title'=>$title,'text'=>$text,'width'=>$size[0],'height'=>$size[1]];}return $items;}
 public static function highlights():array{return array_map(fn($h)=>['photo'=>self::photo($h[0]),'title'=>$h[1],'text'=>$h[2]],self::HIGHLIGHTS);}
 public static function founder():?array{$file='assets/foto/kegiatan/pendiri-kh-agus-salim.jpg';$path=public_path($file);if(!is_file($path)||is_link($path)||!($size=@getimagesize($path)))return null;return ['src'=>asset($file),'width'=>$size[0],'height'=>$size[1]];}
}
