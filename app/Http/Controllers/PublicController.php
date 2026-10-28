<?php
namespace App\Http\Controllers;
use App\Models\{ContentPage,Article,Setting,Asset};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PublicController extends Controller {
 private function settings():array{$s=Setting::pluck('value','key')->all();$s['contact_address']=$s['address']??'';$s['whatsapp']=$s['whatsapp_url']??'';return $s;}
 public function home(){return view('public.home',['units'=>ContentPage::published()->where('type','unit')->get(),'news'=>Article::published()->where('kind','news')->latest('published_at')->limit(3)->get(),'settings'=>$this->settings()]);}
 public function page(string $slug){$page=ContentPage::published()->where('type','page')->where('slug',$slug)->firstOrFail();$view=match($slug){'tentang'=>'public.about','fasilitas'=>'public.facilities','pendaftaran'=>'public.registration',default=>'public.page'};return view($view,compact('page')+['settings'=>$this->settings()]);}
 public function unit(string $slug){$unit=ContentPage::published()->where('type','unit')->where('slug',$slug)->firstOrFail();return view('public.unit',compact('unit')+['settings'=>$this->settings()]);}
 public function listing(Request $r){$kind=match($r->route()->getName()){'public.gallery'=>'galeri','public.achievements'=>'prestasi',default=>'berita'};$db=match($kind){'galeri'=>'gallery','prestasi'=>'achievement',default=>'news'};return view('public.listing',['kind'=>$kind,'items'=>Article::published()->where('kind',$db)->latest('published_at')->paginate(12),'settings'=>$this->settings()]);}
 public function media(Asset $asset){abort_unless($asset->visibility==='public'&&str_starts_with($asset->mime_type,'image/')&&Article::published()->where('cover_asset_id',$asset->id)->exists(),404);abort_unless(Storage::disk('private')->exists($asset->path),404);return response()->file(Storage::disk('private')->path($asset->path),['Content-Type'=>$asset->mime_type,'Cache-Control'=>'public, max-age=3600']);}
 public function sitemap(){ $urls=[route('public.home'),route('public.achievements')];foreach(ContentPage::published()->get() as $p)$urls[]=$p->type==='unit'?route('public.unit',$p->slug):(in_array($p->slug,['tentang','fasilitas','pendaftaran','kontak'])?route('public.page',$p->slug):route('public.content',$p->slug));$xml='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';foreach($urls as $url)$xml.='<url><loc>'.htmlspecialchars($url,ENT_XML1).'</loc></url>';return response($xml.'</urlset>',200,['Content-Type'=>'application/xml']);}
 public function robots(){return response("User-agent: *\nDisallow: /admin\nDisallow: /portal\nDisallow: /berkas\nDisallow: /login\nDisallow: /password\nDisallow: /akun\nSitemap: ".url('/sitemap.xml')."\n",200,['Content-Type'=>'text/plain']);}
}
