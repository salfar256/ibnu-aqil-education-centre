<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Material extends Model {protected $guarded=['id'];protected function casts():array{return ['published_at'=>'datetime'];}public function activity(){return $this->belongsTo(Activity::class);}public function assets(){return $this->hasMany(Asset::class);}}
