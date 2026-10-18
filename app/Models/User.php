<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
 use HasFactory, Notifiable;
 protected $fillable=['name','email','password','school_id','role','is_active','must_change_password'];
 protected $hidden=['password','remember_token'];
 protected function casts():array{return ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean','must_change_password'=>'boolean'];}
 public function school(){return $this->belongsTo(School::class);}
 public function isStaff():bool{return in_array($this->role,['admin','editor'],true);}
}
