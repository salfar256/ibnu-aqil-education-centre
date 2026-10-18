<?php
return ['upload_max_kb'=>(int)env('UPLOAD_MAX_KB',12288),'image_max_pixels'=>20000000,'storage_budget_mb'=>(int)env('STORAGE_BUDGET_MB',10240),'controlled_video_enabled'=>false,'audit_retention_days'=>90];
