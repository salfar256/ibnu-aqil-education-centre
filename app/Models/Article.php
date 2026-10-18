<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Article extends Model {protected $guarded=['id'];protected function casts():array{return ['verified_at'=>'datetime','published_at'=>'datetime'];}public function scopePublished($q){return $q->where('status','published')->whereNotNull('verified_at')->where('published_at','<=',now());}public function cover(){return $this->belongsTo(Asset::class,'cover_asset_id');}}
