<?php

namespace App\Services;

use App\Models\Share;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ShareService
{
    public static function shareItem(
        Model $item,
        Model $recipient,
        string $permission,
        User $sharedBy,
        ?Request $request = null
    ): Share {
        $share = Share::updateOrCreate(
            [
                'shareable_type' => get_class($item),
                'shareable_id' => $item->id,
                'shared_to_type' => get_class($recipient),
                'shared_to_id' => $recipient->id,
            ],
            [
                'shared_by' => $sharedBy->id,
                'permission' => $permission,
            ]
        );

        ActivityLogService::log($sharedBy->id, 'share', $item, [
            'shared_to' => class_basename($recipient) . '#' . $recipient->id,
            'permission' => $permission,
        ], $request);

        return $share;
    }

    public static function unshareItem(Model $item, Model $recipient, User $user, ?Request $request = null): void
    {
        Share::where('shareable_type', get_class($item))
            ->where('shareable_id', $item->id)
            ->where('shared_to_type', get_class($recipient))
            ->where('shared_to_id', $recipient->id)
            ->delete();

        ActivityLogService::log($user->id, 'unshare', $item, [
            'unshared_from' => class_basename($recipient) . '#' . $recipient->id,
        ], $request);
    }

    public static function getPermission(Model $item, User $user): ?string
    {
        // Owner always has manager
        if (method_exists($item, 'user') && $item->user_id === $user->id) {
            return 'manager';
        }

        // Direct share to user
        $share = Share::where('shareable_type', get_class($item))
            ->where('shareable_id', $item->id)
            ->where('shared_to_type', User::class)
            ->where('shared_to_id', $user->id)
            ->first();

        if ($share) return $share->permission;

        // Share to unit kerja
        if ($user->unit_kerja_id) {
            $unitShare = Share::where('shareable_type', get_class($item))
                ->where('shareable_id', $item->id)
                ->where('shared_to_type', \App\Models\UnitKerja::class)
                ->where('shared_to_id', $user->unit_kerja_id)
                ->first();

            if ($unitShare) return $unitShare->permission;
        }

        return null;
    }
}
