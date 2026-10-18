<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AccessGrant extends Model {protected $guarded=['id'];protected function casts():array{return ['expires_at'=>'datetime','allow_download'=>'boolean','is_active'=>'boolean'];}public function school(){return $this->belongsTo(School::class);}public function activity(){return $this->belongsTo(Activity::class);}}
