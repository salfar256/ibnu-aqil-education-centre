<?php
namespace Database\Seeders;
use App\Models\{ContentPage,School,Setting};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
 public function run():void {
 $d=json_decode(file_get_contents(__DIR__.'/data/verified.json'),true,512,JSON_THROW_ON_ERROR);
 School::firstOrCreate(['code'=>'IBNU-AQIL'],['name'=>'Ibnu Aqil Education Centre','is_active'=>true]);
 $source='Brosur resmi TP 2026/2027 dan brosur unit di folder Canva "Brosur"; dikonfirmasi pengguna 19 Oktober 2026. '.$d['verification']['source_url'];
 foreach($d['units'] as $u)ContentPage::firstOrCreate(['slug'=>$u['slug']],['title'=>$u['name'],'type'=>'unit','summary'=>$u['excerpt'],'body'=>$u['body']."\n\nProgram yang diperkenalkan\n• ".implode("\n• ",$u['programs']),'status'=>'published','verified_at'=>'2026-10-19 00:00:00','source_note'=>$source]);
 $pages=[
 'tentang'=>['Tentang Ibnu Aqil',$d['about']['excerpt'],$d['about']['body']],
 'fasilitas'=>['Fasilitas & program','Masjid tiga lantai, laboratorium, asrama, lapangan, dan lebih dari 25 ekstrakurikuler untuk mendukung ilmu, ibadah, dan bakat.','Fasilitas yang diperkenalkan dalam brosur resmi meliputi:\n• '.implode("\n• ",$d['facilities'])."\n\nPembiasaan keagamaan\n• ".implode("\n• ",$d['foundation_programs'])."\n\nPilihan ekstrakurikuler\n• ".implode("\n• ",$d['extracurriculars'])."\n\nKetersediaan fasilitas dan program pada masing-masing unit dapat dikonfirmasi kepada pengelola."],
 'pendaftaran'=>[$d['admissions']['title'],'Alur, persyaratan, dan kontak admin PPDB Ibnu Aqil untuk tahun ajaran 2026/2027.',$d['admissions']['body']],
 'kontak'=>['Mari terhubung','WhatsApp PPDB, telepon kantor, lokasi kampus, dan media sosial resmi Ibnu Aqil.','Sampaikan pertanyaan Anda tentang unit pendidikan, pesantren, pendaftaran, atau Portal Pengimbasan. Untuk PPDB, gunakan WhatsApp PPDB. Ikuti juga kabar kegiatan Ibnu Aqil melalui Instagram, YouTube, dan TikTok @ibnuaqilmedia.']
 ];
 foreach($pages as $slug=>$p)ContentPage::firstOrCreate(['slug'=>$slug],['title'=>$p[0],'summary'=>$p[1],'body'=>str_replace('\\n',"\n",$p[2]),'type'=>'page','status'=>'published','verified_at'=>'2026-10-19 00:00:00','source_note'=>$source]);
 foreach(['contact_phone'=>$d['identity']['telephone_display'],'whatsapp_url'=>$d['admissions']['whatsapp_url'],'registration_url'=>$d['admissions']['url'],'address'=>implode("\n",$d['identity']['addresses']),'contact_email'=>'','maps_url'=>$d['identity']['maps_url'],'instagram_url'=>$d['identity']['instagram_url'],'youtube_url'=>$d['identity']['youtube_url'],'tiktok_url'=>$d['identity']['tiktok_url']] as $k=>$v)Setting::firstOrCreate(['key'=>$k],['value'=>$v]);
 ContentPage::firstOrCreate(['slug'=>'verifikasi-informasi'],['title'=>'Catatan verifikasi sebelum publikasi','type'=>'page','status'=>'draft','summary'=>'Hanya catatan admin, tidak tampil di website.','body'=>'Logo unit sudah diambil dari proyek Canva Logo di YHASB; ganti dengan berkas vektor bila tersedia. PDF Panduan_Menyusun_Praktik_Baik_SMAS_Ibnu_Aqil.pdf belum ditemukan. Nominal biaya PPDB terbaru, tanggal pendaftaran TP 2026/2027, legalitas KBIHU, dan nama pengurus aktif perlu dikonfirmasi sebelum diterbitkan. Jangan menerbitkan e-book berlisensi langganan internal tanpa izin.','source_note'=>$source]);
 }
}
