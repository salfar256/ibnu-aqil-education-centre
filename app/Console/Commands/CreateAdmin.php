<?php
namespace App\Console\Commands;
use App\Models\{User,School};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
class CreateAdmin extends Command {
 protected $signature='app:create-admin {email?}';
 protected $description='Buat admin pertama dengan kata sandi melalui prompt tersembunyi';
 public function handle():int {
 if(User::where('role','admin')->exists()){$this->error('Admin sudah ada. Buat akun berikutnya melalui panel admin.');return self::FAILURE;}
 $email=strtolower($this->argument('email')?:$this->ask('Email admin'));$name=$this->ask('Nama admin');$password=$this->secret('Kata sandi (minimal 12 karakter, huruf dan angka)');$confirmation=$this->secret('Ulangi kata sandi');
 $v=Validator::make(compact('email','name','password')+['password_confirmation'=>$confirmation],['email'=>'required|email|unique:users,email','name'=>'required|string|max:200','password'=>['required','confirmed',Password::min(12)->letters()->numbers()]]);
 if($v->fails()){foreach($v->errors()->all() as $e)$this->error($e);return self::FAILURE;}
 $school=School::firstOrCreate(['code'=>'IBNU-AQIL'],['name'=>'Ibnu Aqil Education Centre','is_active'=>true]);
 User::create(['email'=>$email,'name'=>$name,'password'=>$password,'role'=>'admin','school_id'=>$school->id,'is_active'=>true,'must_change_password'=>false]);
 $this->info('Admin dibuat. Tidak ada kata sandi yang disimpan di file atau ditampilkan.');return self::SUCCESS;
 }
}
