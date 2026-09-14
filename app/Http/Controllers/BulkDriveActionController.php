<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use App\Models\User;
use App\Services\BulkDriveActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkDriveActionController extends Controller
{
    public function __construct(private readonly BulkDriveActionService $bulkDriveActionService)
    {
    }

    public function download(Request $request): StreamedResponse
    {
        $selection = $this->validateSelection($request);
        $archivePath = $this->bulkDriveActionService->createArchive(
            $selection['document_ids'] ?? [],
            $selection['folder_ids'] ?? [],
            $request->user(),
        );

        return response()->streamDownload(function () use ($archivePath): void {
            try {
                readfile($archivePath);
            } finally {
                @unlink($archivePath);
            }
        }, 'simadu-selection-'.now()->format('Ymd-His').'.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function share(Request $request): RedirectResponse
    {
        $selection = $this->validateSelection($request);
        $shareData = $request->validate([
            'shared_to_type' => ['required', 'in:user,unit'],
            'shared_to_id' => ['required', 'integer'],
            'permission' => ['required', 'in:viewer,editor,manager'],
        ]);
        $recipient = $shareData['shared_to_type'] === 'user'
            ? User::findOrFail($shareData['shared_to_id'])
            : UnitKerja::findOrFail($shareData['shared_to_id']);

        $count = $this->bulkDriveActionService->shareSelection(
            $selection['document_ids'] ?? [],
            $selection['folder_ids'] ?? [],
            $recipient,
            $shareData['permission'],
            $request->user(),
            $request,
        );

        return back()->with('success', "$count item berhasil dibagikan.");
    }

    /** @return array{document_ids?: array<int, int>, folder_ids?: array<int, int>} */
    private function validateSelection(Request $request): array
    {
        return $request->validate([
            'document_ids' => ['required_without:folder_ids', 'array', 'min:1'],
            'document_ids.*' => ['integer', 'distinct', 'exists:documents,id'],
            'folder_ids' => ['required_without:document_ids', 'array', 'min:1'],
            'folder_ids.*' => ['integer', 'distinct', 'exists:folders,id'],
        ]);
    }
}
