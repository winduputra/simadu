<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    public static function getNasDisk(): string
    {
        return 'nas';
    }

    public static function uploadFile(
        UploadedFile $file,
        User $user,
        ?int $folderId = null,
        ?int $categoryId = null,
        ?Request $request = null
    ): Document {
        if (!StorageQuotaService::checkQuota($user, $file->getSize())) {
            throw new \Exception('Storage quota exceeded.');
        }

        $disk = static::getNasDisk();
        $directory = date('Y/m');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);

        $document = Document::create([
            'folder_id' => $folderId,
            'user_id' => $user->id,
            'document_category_id' => $categoryId,
            'nama' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'nama_file_asli' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'ukuran' => $file->getSize(),
            'storage_path' => $path,
            'current_version' => 1,
        ]);

        DocumentVersion::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'versi' => 1,
            'nama_file' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'ukuran' => $file->getSize(),
            'storage_path' => $path,
            'created_at' => now(),
        ]);

        StorageQuotaService::addUsage($user, $file->getSize());

        ActivityLogService::log($user->id, 'upload', $document, [
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ], $request);

        return $document;
    }

    public static function deleteFile(Document $document, User $user, ?Request $request = null): void
    {
        $document->delete();
        ActivityLogService::log($user->id, 'delete', $document, null, $request);
    }

    public static function restoreFile(Document $document, User $user, ?Request $request = null): void
    {
        $document->restore();
        ActivityLogService::log($user->id, 'restore', $document, null, $request);
    }

    public static function forceDeleteFile(Document $document, User $user, ?Request $request = null): void
    {
        $disk = static::getNasDisk();

        // Delete all version files from NAS
        foreach ($document->versions as $version) {
            Storage::disk($disk)->delete($version->storage_path);
        }
        Storage::disk($disk)->delete($document->storage_path);

        $size = $document->ukuran;
        $owner = $document->user;

        ActivityLogService::log($user->id, 'force_delete', $document, [
            'filename' => $document->nama_file_asli,
        ], $request);

        $document->forceDelete();
        StorageQuotaService::reduceUsage($owner, $size);
    }

    public static function renameFile(Document $document, string $newName, User $user, ?Request $request = null): void
    {
        $oldName = $document->nama;
        $document->update(['nama' => $newName]);
        ActivityLogService::log($user->id, 'rename', $document, [
            'old_name' => $oldName, 'new_name' => $newName,
        ], $request);
    }

    public static function moveFile(Document $document, ?int $folderId, User $user, ?Request $request = null): void
    {
        $oldFolderId = $document->folder_id;
        $document->update(['folder_id' => $folderId]);
        ActivityLogService::log($user->id, 'move', $document, [
            'from_folder' => $oldFolderId, 'to_folder' => $folderId,
        ], $request);
    }
}
