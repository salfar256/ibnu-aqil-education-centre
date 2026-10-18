<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Activity extends Model {protected $guarded=['id'];public function materials(){return $this->hasMany(Material::class);}public function grants(){return $this->hasMany(AccessGrant::class);}}
