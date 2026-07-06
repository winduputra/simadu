<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class VersionService
{
    public static function uploadNewVersion(
        Document $document,
        UploadedFile $file,
        User $user,
        ?string $catatan = null,
        ?Request $request = null
    ): DocumentVersion {
        if (!StorageQuotaService::checkQuota($user, $file->getSize())) {
            throw new \Exception('Storage quota exceeded.');
        }

        $disk = FileManagerService::getNasDisk();
        $directory = date('Y/m');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, $disk);

        $newVersi = $document->current_version + 1;

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'versi' => $newVersi,
            'nama_file' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'ukuran' => $file->getSize(),
            'storage_path' => $path,
            'catatan' => $catatan,
            'created_at' => now(),
        ]);

        $document->update([
            'current_version' => $newVersi,
            'mime_type' => $file->getClientMimeType(),
            'ukuran' => $file->getSize(),
            'storage_path' => $path,
            'nama_file_asli' => $file->getClientOriginalName(),
        ]);

        StorageQuotaService::addUsage($document->user, $file->getSize());

        ActivityLogService::log($user->id, 'version_upload', $document, [
            'version' => $newVersi, 'filename' => $file->getClientOriginalName(),
        ], $request);

        return $version;
    }

    public static function rollback(Document $document, int $versi, User $user, ?Request $request = null): void
    {
        $version = $document->versions()->where('versi', $versi)->firstOrFail();

        $document->update([
            'current_version' => $version->versi,
            'mime_type' => $version->mime_type,
            'ukuran' => $version->ukuran,
            'storage_path' => $version->storage_path,
            'nama_file_asli' => $version->nama_file,
        ]);

        ActivityLogService::log($user->id, 'version_rollback', $document, [
            'rolled_back_to' => $versi,
        ], $request);
    }
}
