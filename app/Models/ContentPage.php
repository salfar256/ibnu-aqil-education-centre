<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ContentPage extends Model {protected $guarded=['id'];protected function casts():array{return ['verified_at'=>'datetime'];}public function scopePublished($q){return $q->where('status','published')->whereNotNull('verified_at');}}
