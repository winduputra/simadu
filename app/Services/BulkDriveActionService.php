<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class BulkDriveActionService
{
    /** @param array<int, int> $documentIds @param array<int, int> $folderIds */
    public function createArchive(array $documentIds, array $folderIds, User $user): string
    {
        [$documents, $folders] = $this->resolveSelection($documentIds, $folderIds, $user);
        $archivePath = tempnam(sys_get_temp_dir(), 'simadu-selection-');
        if ($archivePath === false) {
            throw new RuntimeException('Failed to allocate bulk download archive.');
        }

        $archive = new ZipArchive();
        if ($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($archivePath);
            throw new RuntimeException('Failed to create bulk download archive.');
        }

        $addedDocumentIds = [];
        $usedPaths = [];
        foreach ($this->topLevelSelectedFolders($folders) as $folder) {
            $this->addFolderToArchive($archive, $folder, '', $user, $addedDocumentIds, $usedPaths);
        }
        foreach ($documents as $document) {
            $this->addDocumentToArchive($archive, $document, '', $addedDocumentIds, $usedPaths);
        }
        $archive->close();

        return $archivePath;
    }

    /**
     * @param array<int, int> $documentIds
     * @param array<int, int> $folderIds
     */
    public function shareSelection(
        array $documentIds,
        array $folderIds,
        Model $recipient,
        string $permission,
        User $user,
        Request $request,
    ): int {
        [$documents, $folders] = $this->resolveSelection($documentIds, $folderIds, $user);

        DB::transaction(function () use ($documents, $folders, $recipient, $permission, $user, $request): void {
            foreach ($folders->concat($documents) as $item) {
                ShareService::shareItem($item, $recipient, $permission, $user, $request);
            }
        });

        return $documents->count() + $folders->count();
    }

    /**
     * @param array<int, int> $documentIds
     * @param array<int, int> $folderIds
     * @return array{Collection<int, Document>, Collection<int, Folder>}
     */
    private function resolveSelection(array $documentIds, array $folderIds, User $user): array
    {
        $documents = Document::query()->whereIn('id', $documentIds)->get();
        $folders = Folder::query()->whereIn('id', $folderIds)->get();
        abort_if($documents->count() !== count($documentIds), 404);
        abort_if($folders->count() !== count($folderIds), 404);

        foreach ($folders->concat($documents) as $item) {
            $this->authorizeItem($item, $user);
        }

        return [$documents, $folders];
    }

    /** @param array<int, true> $addedDocumentIds @param array<string, true> $usedPaths */
    private function addFolderToArchive(
        ZipArchive $archive,
        Folder $folder,
        string $parentPath,
        User $user,
        array &$addedDocumentIds,
        array &$usedPaths,
    ): void {
        $this->authorizeItem($folder, $user);
        $folderPath = $parentPath.$this->safePathSegment($folder->nama).'/';
        $archive->addEmptyDir(rtrim($folderPath, '/'));

        foreach ($folder->documents()->orderBy('nama')->get() as $document) {
            $this->authorizeItem($document, $user);
            $this->addDocumentToArchive($archive, $document, $folderPath, $addedDocumentIds, $usedPaths);
        }
        foreach ($folder->children()->orderBy('nama')->get() as $child) {
            $this->addFolderToArchive($archive, $child, $folderPath, $user, $addedDocumentIds, $usedPaths);
        }
    }

    /** @param array<int, true> $addedDocumentIds @param array<string, true> $usedPaths */
    private function addDocumentToArchive(
        ZipArchive $archive,
        Document $document,
        string $parentPath,
        array &$addedDocumentIds,
        array &$usedPaths,
    ): void {
        if (isset($addedDocumentIds[$document->id])) {
            return;
        }

        $disk = Storage::disk(FileManagerService::getNasDisk());
        if (!$disk->exists($document->storage_path)) {
            return;
        }

        $path = $this->uniqueArchivePath($parentPath.$this->safePathSegment($document->nama_file_asli), $usedPaths);
        $archive->addFromString($path, $disk->get($document->storage_path));
        $addedDocumentIds[$document->id] = true;
    }

    private function authorizeItem(Model $item, User $user): void
    {
        abort_unless($item->user_id === $user->id || $user->isSuperAdmin(), 403);
    }

    /** @param Collection<int, Folder> $folders @return Collection<int, Folder> */
    private function topLevelSelectedFolders(Collection $folders): Collection
    {
        $selectedIds = array_fill_keys($folders->modelKeys(), true);

        return $folders->filter(function (Folder $folder) use ($selectedIds): bool {
            foreach ($folder->ancestors() as $ancestor) {
                if (isset($selectedIds[$ancestor->id])) {
                    return false;
                }
            }

            return true;
        });
    }

    private function safePathSegment(string $name): string
    {
        $safeName = trim(str_replace(['/', '\\'], '-', $name));
        if ($safeName === '.' || $safeName === '..') {
            return 'item';
        }

        return $safeName !== '' ? $safeName : 'item';
    }

    /** @param array<string, true> $usedPaths */
    private function uniqueArchivePath(string $path, array &$usedPaths): string
    {
        if (!isset($usedPaths[$path])) {
            $usedPaths[$path] = true;

            return $path;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $base = $extension === '' ? $path : substr($path, 0, -(strlen($extension) + 1));
        for ($suffix = 2; ; $suffix++) {
            $candidate = $base." ($suffix)".($extension === '' ? '' : ".$extension");
            if (!isset($usedPaths[$candidate])) {
                $usedPaths[$candidate] = true;

                return $candidate;
            }
        }
    }
}
