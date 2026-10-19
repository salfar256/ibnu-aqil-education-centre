<?php
namespace App\Services;
// Official display photos live in public/assets/foto. A slot only renders when its file exists, so missing photos never show a broken image.
class PublicPhotos {
 private const SLOTS=[
  'masjid'=>['file'=>'masjid-ibnu-aqil.jpg','alt'=>'Masjid di lingkungan Ibnu Aqil, Yayasan Haji Agus Salim Bogor','caption'=>'Masjid Ibnu Aqil','tag'=>'Dokumentasi'],
  'manasik'=>['file'=>'manasik-kabah.jpg','alt'=>'Replika Ka\'bah untuk praktik manasik di halaman masjid Ibnu Aqil','caption'=>'Area praktik manasik','tag'=>'Dokumentasi'],
  'ilustrasi'=>['file'=>'ilustrasi-gerbang.jpg','alt'=>'Ilustrasi santri dan pelajar memasuki gerbang Ibnu Aqil','caption'=>'Ilustrasi Ke-Ibnu Aqil-an','tag'=>'Ilustrasi'],
 ];
 public static function all():array{$photos=[];foreach(self::SLOTS as $key=>$slot)if($photo=self::get($key))$photos[$key]=$photo;return $photos;}
 public static function get(string $key):?array{$slot=self::SLOTS[$key]??null;if(!$slot)return null;$path=public_path('assets/foto/'.$slot['file']);if(!is_file($path)||is_link($path)||!($size=@getimagesize($path)))return null;return $slot+['src'=>asset('assets/foto/'.$slot['file']),'width'=>$size[0],'height'=>$size[1]];}
 // Official unit logos from the Canva project "Logo di YHASB"; null when the file is missing so views can fall back to text.
 public static function unitLogo(string $slug):?string{if(!preg_match('/^[a-z]+$/',$slug))return null;$path=public_path('assets/logo/'.$slug.'.png');return is_file($path)&&!is_link($path)?'assets/logo/'.$slug.'.png':null;}
 public static function logo():string{return is_file(public_path('assets/foto/logo-yhasb-hd.png'))?'assets/foto/logo-yhasb-hd.png':'assets/logo-yhasb.png';}
}
