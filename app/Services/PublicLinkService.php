<?php

namespace App\Services;

use App\Models\PublicLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicLinkService
{
    public static function createLink(
        Model $item,
        User $user,
        string $permission = 'viewer',
        ?string $password = null,
        ?\DateTimeInterface $expiresAt = null,
        ?int $maxAccess = null,
        ?Request $request = null
    ): PublicLink {
        $link = PublicLink::create([
            'linkable_type' => $item->getMorphClass(),
            'linkable_id' => $item->id,
            'user_id' => $user->id,
            'token' => Str::random(64),
            'permission' => $permission,
            'has_password' => !empty($password),
            'password' => $password,
            'expires_at' => $expiresAt,
            'max_access_count' => $maxAccess,
        ]);

        ActivityLogService::log($user->id, 'public_link_create', $item, [
            'token' => substr($link->token, 0, 8) . '...',
        ], $request);

        return $link;
    }

    public static function revokeLink(PublicLink $link, User $user, ?Request $request = null): void
    {
        $link->update(['is_active' => false]);
        ActivityLogService::log($user->id, 'public_link_revoke', $link->linkable, null, $request);
    }

    public static function findByToken(string $token): ?PublicLink
    {
        return PublicLink::where('token', $token)->with('linkable')->first();
    }
}
