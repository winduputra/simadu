<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;

class FolderService
{
    public static function create(array $data, User $user, ?Request $request = null): Folder
    {
        $folder = Folder::create(array_merge($data, ['user_id' => $user->id]));
        ActivityLogService::log($user->id, 'folder_create', $folder, null, $request);
        return $folder;
    }

    public static function rename(Folder $folder, string $newName, User $user, ?Request $request = null): void
    {
        $oldName = $folder->nama;
        $folder->update(['nama' => $newName]);
        ActivityLogService::log($user->id, 'rename', $folder, [
            'old_name' => $oldName, 'new_name' => $newName,
        ], $request);
    }

    public static function move(Folder $folder, ?int $parentId, User $user, ?Request $request = null): void
    {
        $oldParent = $folder->parent_id;
        $folder->update(['parent_id' => $parentId]);
        ActivityLogService::log($user->id, 'move', $folder, [
            'from_parent' => $oldParent, 'to_parent' => $parentId,
        ], $request);
    }

    public static function delete(Folder $folder, User $user, ?Request $request = null): void
    {
        $folder->delete();
        ActivityLogService::log($user->id, 'delete', $folder, null, $request);
    }

    public static function restore(Folder $folder, User $user, ?Request $request = null): void
    {
        $folder->restore();
        ActivityLogService::log($user->id, 'restore', $folder, null, $request);
    }

    public static function forceDelete(Folder $folder, User $user, ?Request $request = null): void
    {
        ActivityLogService::log($user->id, 'force_delete', $folder, [
            'folder_name' => $folder->nama,
        ], $request);
        $folder->forceDelete();
    }
}
