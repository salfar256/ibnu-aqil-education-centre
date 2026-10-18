<?php
namespace App\Services;
use App\Models\{Material,User,AccessGrant};
class MaterialAccess {
 public function query(User $user){$q=Material::with('activity')->withCount('assets');if($user->isStaff())return $q;return $q->where('status','published')->where('published_at','<=',now())->whereHas('activity',fn($q)=>$q->where('status','published')->whereHas('grants',fn($g)=>$g->where('school_id',$user->school_id)->where('is_active',true)->where(fn($e)=>$e->whereNull('expires_at')->orWhere('expires_at','>',now()))));}
 public function allows(User $user,Material $material,bool $download=false):bool{if(!$user->is_active||!$user->school?->is_active)return false;if($user->isStaff())return true;if(!$this->query($user)->whereKey($material->id)->exists())return false;return !$download||AccessGrant::where('school_id',$user->school_id)->where('activity_id',$material->activity_id)->where('is_active',true)->where('allow_download',true)->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->exists();}
}
