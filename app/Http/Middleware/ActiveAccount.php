<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ActiveAccount {public function handle(Request $r,Closure $next){$u=$r->user();if(!$u?->is_active||!$u->school?->is_active){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();abort(403,'Akun atau sekolah dinonaktifkan. Hubungi admin.');}if($u->must_change_password&&!$r->routeIs('profile.*','logout'))return redirect()->route('profile.edit')->with('status','Atur kata sandi pribadi sebelum melanjutkan.');return $next($r);}}
