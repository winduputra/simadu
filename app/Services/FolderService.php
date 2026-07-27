<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class FolderService
{
    public static function create(array $data, User $user, ?Request $request = null): Folder
    {
        $folder = Folder::create(array_merge($data, ['user_id' => $user->id]));
        
        // Buat folder fisik di NAS
        $nasPath = FileManagerService::getTargetDirectory($user, $folder->id);
        Storage::disk(FileManagerService::getNasDisk())->makeDirectory($nasPath);

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
        // Soft delete all child documents
        \App\Models\Document::where('folder_id', $folder->id)->each(function ($doc) use ($user, $request) {
            FileManagerService::deleteFile($doc, $user, $request);
        });

        // Soft delete all child folders
        Folder::where('parent_id', $folder->id)->each(function ($child) use ($user, $request) {
            static::delete($child, $user, $request);
        });

        $folder->delete();
        ActivityLogService::log($user->id, 'delete', $folder, null, $request);
    }

    public static function restore(Folder $folder, User $user, ?Request $request = null): void
    {
        $folder->restore();

        // Restore child folders
        Folder::onlyTrashed()->where('parent_id', $folder->id)->each(function ($child) use ($user, $request) {
            static::restore($child, $user, $request);
        });

        // Restore child documents
        \App\Models\Document::onlyTrashed()->where('folder_id', $folder->id)->each(function ($doc) use ($user, $request) {
            FileManagerService::restoreFile($doc, $user, $request);
        });

        ActivityLogService::log($user->id, 'restore', $folder, null, $request);
    }

    public static function forceDelete(Folder $folder, User $user, ?Request $request = null): void
    {
        // 1. Force delete child documents (trashed and non-trashed)
        $documents = \App\Models\Document::withTrashed()->where('folder_id', $folder->id)->get();
        foreach ($documents as $doc) {
            FileManagerService::forceDeleteFile($doc, $user, $request);
        }

        // 2. Force delete child folders recursively (trashed and non-trashed)
        $children = Folder::withTrashed()->where('parent_id', $folder->id)->get();
        foreach ($children as $child) {
            static::forceDelete($child, $user, $request);
        }

        // 3. Get NAS path before database deletion
        $nasPath = FileManagerService::getTargetDirectory($user, $folder->id);

        ActivityLogService::log($user->id, 'force_delete', $folder, [
            'folder_name' => $folder->nama,
        ], $request);

        // 4. Force delete current folder from DB
        $folder->forceDelete();

        // 5. Delete physical NAS directory if exists
        $disk = FileManagerService::getNasDisk();
        if (Storage::disk($disk)->exists($nasPath)) {
            Storage::disk($disk)->deleteDirectory($nasPath);
        }
    }
}
