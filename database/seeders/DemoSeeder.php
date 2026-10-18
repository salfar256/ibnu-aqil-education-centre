<?php
namespace Database\Seeders;
use App\Models\{Activity,Material,School,AccessGrant,Asset};
use Illuminate\Database\Seeder;
class DemoSeeder extends Seeder {
 public function run():void {
 if(!app()->environment(['local','testing']))throw new \RuntimeException('DemoSeeder hanya untuk lingkungan lokal/pengujian.');
 foreach(['A','B'] as $code)School::firstOrCreate(['code'=>'DEMO-'.$code],['name'=>'Sekolah Contoh '.$code.' (Data Pengembangan)','is_active'=>true]);
 $activity=Activity::firstOrCreate(['slug'=>'contoh-berbagi-praktik-baik'],['title'=>'[CONTOH] Berbagi Praktik Baik','theme'=>'Pengembangan Pembelajaran','year'=>2026,'description'=>'Data contoh untuk pengujian portal; bukan laporan kegiatan lembaga.','status'=>'published']);
 foreach(['module'=>'Menyusun refleksi pembelajaran','ebook'=>'Panduan berbagi praktik baik','photo'=>'Galeri kegiatan belajar','video'=>'Diskusi praktik pembelajaran'] as $type=>$title)Material::firstOrCreate(['slug'=>'contoh-'.$type],['activity_id'=>$activity->id,'title'=>'[CONTOH] '.$title,'description'=>'Materi pengembangan untuk menguji akses portal. Unggah materi berizin melalui panel admin. Data ini tidak diterbitkan ke website publik.','type'=>$type,'author_name'=>'Tim Contoh (Pengembangan)','status'=>$type==='video'?'draft':'published','published_at'=>now(),'video_protection'=>'link']);
 $m=Material::where('slug','contoh-module')->first();
 $path='demo/contoh-refleksi.pdf';$disk=\Illuminate\Support\Facades\Storage::disk('private');$disk->put($path,file_get_contents(__DIR__.'/demo-files/contoh-refleksi.pdf'));
 Asset::firstOrCreate(['path'=>$path],['material_id'=>$m->id,'original_name'=>'CONTOH-lembar-refleksi.pdf','mime_type'=>'application/pdf','size'=>$disk->size($path),'visibility'=>'private','allow_download'=>true]);
 AccessGrant::firstOrCreate(['school_id'=>School::where('code','DEMO-A')->first()->id,'activity_id'=>$activity->id],['is_active'=>true,'allow_download'=>true]);
 }
}
