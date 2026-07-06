<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        int $userId,
        string $aksi,
        ?Model $loggable = null,
        ?array $detail = null,
        ?Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'aksi' => $aksi,
            'detail' => $detail,
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
            'created_at' => now(),
        ]);
    }
}
