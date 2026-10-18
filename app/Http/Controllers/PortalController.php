<?php
namespace App\Http\Controllers;
use App\Models\{Material,Asset};
use App\Services\{MaterialAccess,Audit};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PortalController extends Controller {
 public function index(Request $r,MaterialAccess $access){
 $r->validate(['q'=>'nullable|string|max:120','theme'=>'nullable|string|max:120','year'=>'nullable|integer|between:2000,2100','type'=>'nullable|in:pdf,ebook,module,photo,video','activity'=>'nullable|integer']);
 $base=$access->query($r->user());$activities=(clone $base)->get()->pluck('activity')->unique('id')->sortByDesc('year');
 $q=clone $base;if($r->filled('q'))$q->where(fn($q)=>$q->where('title','like','%'.$r->q.'%')->orWhere('description','like','%'.$r->q.'%'));
 foreach(['year','theme'] as $f)if($r->filled($f))$q->whereHas('activity',fn($q)=>$q->where($f,$r->input($f)));if($r->filled('type'))$q->where('type',$r->type);if($r->filled('activity'))$q->where('activity_id',$r->activity);
 return view('portal.index',['materials'=>$q->latest('published_at')->paginate(12)->withQueryString(),'activities'=>$activities,'total'=>(clone $base)->count()]);
 }
 public function show(Request $r,Material $material,MaterialAccess $access){abort_unless($access->allows($r->user(),$material),404);Audit::record('material.view',$material);$embed=null;if($material->type==='video'&&$material->video_protection==='link'){$id=$material->video_id;if($material->video_provider==='youtube'&&preg_match('/^[a-zA-Z0-9_-]{11}$/',$id??''))$embed='https://www.youtube-nocookie.com/embed/'.$id;if($material->video_provider==='vimeo'&&preg_match('/^[0-9]{6,12}$/',$id??''))$embed='https://player.vimeo.com/video/'.$id;}return view('portal.show',['material'=>$material->load('activity','assets'),'canDownload'=>$access->allows($r->user(),$material,true),'embed'=>$embed]);}
 public function file(Request $r,Asset $asset,MaterialAccess $access){$material=$asset->material;abort_unless($material&&$asset->visibility==='private'&&$access->allows($r->user(),$material),404);$download=$r->boolean('download');if($download)abort_unless($asset->allow_download&&$access->allows($r->user(),$material,true),403);abort_unless(Storage::disk('private')->exists($asset->path),404);Audit::record($download?'asset.download':'asset.read',$asset);$headers=['Content-Type'=>$asset->mime_type,'Cache-Control'=>'private, no-store, max-age=0','X-Content-Type-Options'=>'nosniff'];return $download?response()->download(Storage::disk('private')->path($asset->path),$asset->original_name,$headers):response()->file(Storage::disk('private')->path($asset->path),$headers);}
}
