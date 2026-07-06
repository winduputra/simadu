<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\User;

class StorageQuotaService
{
    public static function checkQuota(User $user, int $fileSize): bool
    {
        return $user->hasQuotaFor($fileSize);
    }

    public static function addUsage(User $user, int $bytes): void
    {
        $user->increment('storage_used', $bytes);
    }

    public static function reduceUsage(User $user, int $bytes): void
    {
        $newUsed = max(0, $user->storage_used - $bytes);
        $user->update(['storage_used' => $newUsed]);
    }

    public static function recalculate(User $user): void
    {
        $total = $user->documents()->withTrashed()->sum('ukuran');
        $user->update(['storage_used' => $total]);
    }

    public static function getDefaultQuota(): int
    {
        return (int) SystemSetting::get('default_storage_quota', 0);
    }
}
