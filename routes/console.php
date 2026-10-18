<?php
use Illuminate\Support\Facades\Schedule;
use App\Models\AuditLog;
Schedule::call(fn()=>AuditLog::where('created_at','<',now()->subDays(config('portal.audit_retention_days')))->delete())->daily();
