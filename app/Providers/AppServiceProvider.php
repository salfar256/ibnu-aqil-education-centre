<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider {
 public function register():void{}
 public function boot():void{
  ResetPassword::createUrlUsing(fn($user,string $token)=>rtrim(config('app.url'),'/').'/password/reset/'.rawurlencode($token).'?email='.rawurlencode($user->getEmailForPasswordReset()));
  if(app()->environment('production')){URL::forceRootUrl(config('app.url'));URL::forceScheme((string)parse_url(config('app.url'),PHP_URL_SCHEME));}
 }
}
