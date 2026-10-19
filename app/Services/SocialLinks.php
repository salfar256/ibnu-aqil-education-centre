<?php
namespace App\Services;
// Official social and map links. Each setting only renders when it matches its own platform, so a mistyped or foreign URL never reaches the public site.
class SocialLinks {
 public const PATTERNS=[
  'instagram_url'=>'~^https://(?:www\.)?instagram\.com/[A-Za-z0-9._]{1,30}/?$~',
  'youtube_url'=>'~^https://(?:www\.|m\.)?youtube\.com/(?:@[A-Za-z0-9._-]{3,30}|channel/[A-Za-z0-9_-]{24})/?$~',
  'tiktok_url'=>'~^https://(?:www\.)?tiktok\.com/@[A-Za-z0-9._]{2,24}/?$~',
  'maps_url'=>'~^https://(?:www\.google\.com/maps/|maps\.google\.com/|maps\.app\.goo\.gl/|goo\.gl/maps/)[^\s<>"\']*$~',
 ];
 private const LABELS=['instagram_url'=>'Instagram','youtube_url'=>'YouTube','tiktok_url'=>'TikTok'];
 public static function valid(string $key,?string $url):bool{return isset(self::PATTERNS[$key])&&is_string($url)&&strlen($url)<=500&&preg_match(self::PATTERNS[$key],$url)===1;}
 public static function social(array $settings):array{$links=[];foreach(self::LABELS as $key=>$label)if(self::valid($key,$settings[$key]??null))$links[$key]=['label'=>$label,'url'=>$settings[$key],'handle'=>self::handle($settings[$key])];return $links;}
 public static function maps(array $settings):?string{return self::valid('maps_url',$settings['maps_url']??null)?$settings['maps_url']:null;}
 private static function handle(string $url):string{$path=trim((string)parse_url($url,PHP_URL_PATH),'/');return str_starts_with($path,'@')?$path:'@'.basename($path);}
}
