<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\VersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VersionController extends Controller
{
    public function store(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|max:204800',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            VersionService::uploadNewVersion(
                $document, $request->file('file'), $request->user(), $request->catatan, $request
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'New version uploaded.');
    }

    public function download(Request $request, Document $document, int $versi)
    {
        $version = $document->versions()->where('versi', $versi)->firstOrFail();
        $disk = \App\Services\FileManagerService::getNasDisk();

        if (!Storage::disk($disk)->exists($version->storage_path)) {
            abort(404, 'Version file not found.');
        }

        return Storage::disk($disk)->download($version->storage_path, $version->nama_file);
    }

    public function rollback(Request $request, Document $document, int $versi)
    {
        VersionService::rollback($document, $versi, $request->user(), $request);
        return back()->with('success', "Rolled back to version {$versi}.");
    }
}
