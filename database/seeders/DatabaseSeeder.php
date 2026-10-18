<?php
namespace Database\Seeders;
use App\Models\{ContentPage,School,Setting};
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
 public function run():void {
 $d=json_decode(file_get_contents(__DIR__.'/data/verified.json'),true,512,JSON_THROW_ON_ERROR);
 School::firstOrCreate(['code'=>'IBNU-AQIL'],['name'=>'Ibnu Aqil Education Centre','is_active'=>true]);
 $source='Brosur resmi TP 2026/2027; dikonfirmasi pengguna 18 Oktober 2026. '.$d['verification']['source_url'];
 foreach($d['units'] as $u)ContentPage::firstOrCreate(['slug'=>$u['slug']],['title'=>$u['name'],'type'=>'unit','summary'=>$u['excerpt'],'body'=>$u['body']."\n\nProgram yang diperkenalkan\n• ".implode("\n• ",$u['programs']),'status'=>'published','verified_at'=>'2026-10-18 00:00:00','source_note'=>$source]);
 $pages=[
 'tentang'=>['Tentang Ibnu Aqil',$d['about']['excerpt'],$d['about']['body']],
 'fasilitas'=>['Fasilitas & program','Lingkungan belajar untuk mendukung ilmu, ibadah, dan pengembangan minat.','Fasilitas yang diperkenalkan dalam brosur resmi meliputi:\n• '.implode("\n• ",$d['facilities'])."\n\nPembiasaan keagamaan\n• ".implode("\n• ",$d['foundation_programs'])."\n\nPilihan ekstrakurikuler\n• ".implode("\n• ",$d['extracurriculars'])."\n\nKetersediaan fasilitas dan program pada masing-masing unit dapat dikonfirmasi kepada pengelola."],
 'pendaftaran'=>[$d['admissions']['title'],'Kenali pilihan unit dan temukan jalur informasi pendaftaran resmi.',$d['admissions']['body']],
 'kontak'=>['Mari terhubung','Hubungi pengelola melalui jalur komunikasi resmi Ibnu Aqil.','Sampaikan pertanyaan Anda tentang unit pendidikan, pesantren, pendaftaran, atau Portal Pengimbasan. Admin akan membantu mengarahkan Anda ke pengelola yang sesuai.']
 ];
 foreach($pages as $slug=>$p)ContentPage::firstOrCreate(['slug'=>$slug],['title'=>$p[0],'summary'=>$p[1],'body'=>str_replace('\\n',"\n",$p[2]),'type'=>'page','status'=>'published','verified_at'=>'2026-10-18 00:00:00','source_note'=>$source]);
 foreach(['contact_phone'=>$d['identity']['telephone_display'],'whatsapp_url'=>'https://wa.me/'.$d['identity']['whatsapp'],'registration_url'=>$d['admissions']['url'],'address'=>implode("\n",$d['identity']['addresses']),'contact_email'=>'','maps_url'=>'https://www.google.com/maps/search/?api=1&query=Ibnu+Aqil+Education+Center+Laladon+Bogor'] as $k=>$v)Setting::firstOrCreate(['key'=>$k],['value'=>$v]);
 ContentPage::firstOrCreate(['slug'=>'verifikasi-informasi'],['title'=>'Catatan verifikasi sebelum publikasi','type'=>'page','status'=>'draft','summary'=>'Hanya catatan admin, tidak tampil di website.','body'=>'Logo perlu diganti berkas asli resolusi tinggi. PDF Panduan_Menyusun_Praktik_Baik_SMAS_Ibnu_Aqil.pdf belum ditemukan. Biaya PPDB, tanggal pendaftaran, klaim akreditasi, legalitas KBIHU, prestasi, nama pengurus aktif, dan statistik memerlukan dokumen pendukung. Jangan menerbitkan e-book berlisensi langganan internal tanpa izin.','source_note'=>$source]);
 }
}
