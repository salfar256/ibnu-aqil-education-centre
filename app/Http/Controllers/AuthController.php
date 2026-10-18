<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash,Password,RateLimiter};
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
 public function login(){return view('auth.login');}
 public function authenticate(Request $r){
 $data=$r->validate(['email'=>'required|email|max:255','password'=>'required|string|max:255']);$data['email']=Str::lower($data['email']);
 $key='login:'.hash('sha256',$data['email'].'|'.$r->ip());
 if(RateLimiter::tooManyAttempts($key,5))throw ValidationException::withMessages(['email'=>'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.']);
 if(!Auth::attempt([...$data,'is_active'=>true],false)){RateLimiter::hit($key,300);throw ValidationException::withMessages(['email'=>'Email atau kata sandi tidak sesuai.']);}
 if(!$r->user()->school?->is_active){Auth::logout();RateLimiter::hit($key,300);throw ValidationException::withMessages(['email'=>'Akun tidak dapat diakses. Hubungi admin lembaga.']);}
 RateLimiter::clear($key);$r->session()->regenerate();Audit::record('login',$r->user());
 return redirect()->intended($r->user()->isStaff()?route('admin.index'):route('portal.index'));
 }
 public function logout(Request $r){Audit::record('logout',$r->user());Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('login');}
 public function forgot(){return view('auth.forgot');}
 public function email(Request $r){$r->validate(['email'=>'required|email|max:255']);$u=User::where('email',Str::lower($r->email))->where('is_active',true)->first();if($u&&$u->school?->is_active){try{Password::sendResetLink(['email'=>$u->email]);}catch(\Throwable $e){report($e);}}return back()->with('status','Jika akun aktif ditemukan, petunjuk pemulihan akan dikirim ke email terdaftar. Hubungi admin jika email belum diterima.');}
 public function resetForm(Request $r,string $token){return view('auth.reset',['token'=>$token,'email'=>$r->query('email')]);}
 public function reset(Request $r){$data=$r->validate(['token'=>'required|string|max:255','email'=>'required|email','password'=>['required','confirmed',PasswordRule::min(12)->letters()->numbers()]]);$u=User::where('email',Str::lower($data['email']))->first();if(!$u?->is_active||!$u->school?->is_active)throw ValidationException::withMessages(['email'=>'Tautan pemulihan tidak valid.']);
 $status=Password::reset($data,function(User $user,string $password){$user->forceFill(['password'=>Hash::make($password),'must_change_password'=>false,'remember_token'=>Str::random(60)])->save();\Illuminate\Support\Facades\DB::table('sessions')->where('user_id',$user->id)->delete();event(new PasswordReset($user));Audit::record('password.reset',$user);});
 if($status!==Password::PASSWORD_RESET)throw ValidationException::withMessages(['email'=>'Tautan pemulihan tidak valid atau sudah kedaluwarsa.']);return redirect()->route('login')->with('status','Kata sandi diperbarui. Silakan masuk.');
 }
 public function profile(){return view('auth.profile');}
 public function updatePassword(Request $r){$data=$r->validate(['current_password'=>['required','current_password'],'password'=>['required','confirmed','different:current_password',PasswordRule::min(12)->letters()->numbers()]]);$r->user()->forceFill(['password'=>Hash::make($data['password']),'must_change_password'=>false,'remember_token'=>Str::random(60)])->save();\Illuminate\Support\Facades\DB::table('sessions')->where('user_id',$r->user()->id)->where('id','!=',$r->session()->getId())->delete();$r->session()->regenerate();Audit::record('password.changed',$r->user());return redirect()->route('portal.index')->with('status','Kata sandi berhasil diperbarui.');}
}
