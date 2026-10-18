<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Asset extends Model {protected $guarded=['id'];protected function casts():array{return ['allow_download'=>'boolean'];}public function material(){return $this->belongsTo(Material::class);}}
