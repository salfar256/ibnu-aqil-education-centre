<?php
namespace App\Services;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
class Audit {public static function record(string $action,?Model $subject=null):void{AuditLog::create(['user_id'=>auth()->id(),'school_id'=>auth()->user()?->school_id,'action'=>$action,'subject_type'=>$subject?->getMorphClass(),'subject_id'=>$subject?->getKey(),'ip_hash'=>hash_hmac('sha256',request()->ip()??'cli',config('app.key'))]);}}
