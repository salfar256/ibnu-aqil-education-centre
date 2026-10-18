<?php
namespace App\Services;
use App\Models\{Asset,Material};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Storage,Validator};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class AssetStorage {
 public function store(UploadedFile $file,?Material $material=null,string $visibility='private',bool $allowDownload=true):Asset{
 Validator::make(['file'=>$file],['file'=>['required','file','max:'.config('portal.upload_max_kb'),'mimes:pdf,jpg,jpeg,png,webp','extensions:pdf,jpg,jpeg,png,webp']])->validate();
 $mime=$file->getMimeType();$ext=match($mime){'application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp',default=>throw ValidationException::withMessages(['file'=>'Jenis berkas tidak diizinkan.'])};
 if(Asset::sum('size')+$file->getSize()>config('portal.storage_budget_mb')*1024*1024)throw ValidationException::withMessages(['file'=>'Alokasi media penuh. Hubungi admin.']);
 if($material)$visibility='private';$path='media/'.date('Y/m').'/'.Str::uuid().'.'.$ext;
 if($ext==='pdf'){
 if(!str_starts_with(file_get_contents($file->getRealPath(),false,null,0,1024),'%PDF-'))throw ValidationException::withMessages(['file'=>'Isi PDF tidak valid.']);
 $stream=fopen($file->getRealPath(),'rb');$tail='';while(!feof($stream)){$chunk=$tail.fread($stream,65536);$decoded=preg_replace_callback('/#([0-9a-f]{2})/i',fn($m)=>chr(hexdec($m[1])),$chunk);if(preg_match('~/\s*(JavaScript|JS|Launch|EmbeddedFile|RichMedia|OpenAction|AA|XFA|AcroForm|SubmitForm|ImportData|Rendition|Movie|Sound|ObjStm|Encrypt)\b|<\?(php|=)~i',$decoded)){fclose($stream);throw ValidationException::withMessages(['file'=>'PDF memakai fitur aktif, enkripsi, atau object stream terkompresi. Ekspor ulang sebagai PDF standar tanpa fitur tersebut.']);}$tail=substr($chunk,-128);}fclose($stream);
 Storage::disk('private')->putFileAs(dirname($path),$file,basename($path));
 }else{
 $info=@getimagesize($file->getRealPath());if(!$info||$info[0]*$info[1]>config('portal.image_max_pixels'))throw ValidationException::withMessages(['file'=>'Dimensi foto tidak valid (maks. 20 megapiksel).']);
 $image=@imagecreatefromstring(file_get_contents($file->getRealPath()));if(!$image)throw ValidationException::withMessages(['file'=>'Foto tidak dapat dibaca.']);
 $width=min(1920,imagesx($image));$height=max(1,(int)round(imagesy($image)*$width/imagesx($image)));$resized=imagecreatetruecolor($width,$height);imagealphablending($resized,false);imagesavealpha($resized,true);imagecopyresampled($resized,$image,0,0,0,0,$width,$height,imagesx($image),imagesy($image));ob_start();imagewebp($resized,null,82);$bytes=ob_get_clean();imagedestroy($resized);imagedestroy($image);$path=preg_replace('/\.[^.]+$/','.webp',$path);$mime='image/webp';Storage::disk('private')->put($path,$bytes);
 }
 $name=Str::limit(preg_replace('/[\x00-\x1F\x7F]/u','',basename(str_replace('\\','/',$file->getClientOriginalName()))),180,'');
 return Asset::create(['material_id'=>$material?->id,'original_name'=>$name,'path'=>$path,'mime_type'=>$mime,'size'=>Storage::disk('private')->size($path),'visibility'=>$visibility,'allow_download'=>$allowDownload]);
 }
}
