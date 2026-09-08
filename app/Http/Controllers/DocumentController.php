<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\ActivityLogService;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:204800', // 200MB max
            'folder_id' => 'nullable|exists:folders,id',
            'document_category_id' => 'nullable|exists:document_categories,id',
        ]);

        $uploaded = [];
        foreach ($request->file('files', []) as $file) {
            try {
                $uploaded[] = FileManagerService::uploadFile(
                    $file, $request->user(), $request->folder_id, $request->document_category_id, $request
                );
            } catch (\Exception $e) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => $e->getMessage()], 422);
                }
                return back()->with('error', $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => count($uploaded) . ' file(s) uploaded successfully.',
                'uploaded' => $uploaded
            ]);
        }

        return back()->with('success', count($uploaded) . ' file(s) uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load(['user', 'documentCategory', 'versions.user', 'shares.sharedBy']);
        $categories = DocumentCategory::orderBy('nama')->get();
        $users = \App\Models\User::where('id', '!=', auth()->id())->orderBy('nama')->get();
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama')->get();
        return view('document.detail', compact('document', 'categories', 'users', 'unitKerjas'));
    }

    public function download(Request $request, Document $document)
    {
        $disk = FileManagerService::getNasDisk();
        if (!Storage::disk($disk)->exists($document->storage_path)) {
            abort(404, 'File not found on storage.');
        }

        ActivityLogService::log($request->user()->id, 'download', $document, null, $request);

        return Storage::disk($disk)->download($document->storage_path, $document->nama_file_asli);
    }

    public function preview(Request $request, Document $document)
    {
        ActivityLogService::log($request->user()->id, 'preview', $document, null, $request);

        return view('document.preview', [
            'document' => $document,
            'streamUrl' => route('documents.stream', $document),
            'downloadUrl' => route('documents.download', $document),
            'canDownload' => true,
        ]);
    }

    public function stream(Document $document)
    {
        $disk = FileManagerService::getNasDisk();
        if (!Storage::disk($disk)->exists($document->storage_path)) {
            abort(404, 'File not found on storage.');
        }

        return Storage::disk($disk)->response($document->storage_path, $document->nama_file_asli, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . addslashes($document->nama_file_asli) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function rename(Request $request, Document $document)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        FileManagerService::renameFile($document, $request->nama, $request->user(), $request);
        return back()->with('success', 'File renamed successfully.');
    }

    public function move(Request $request, Document $document)
    {
        $request->validate(['folder_id' => 'nullable|exists:folders,id']);
        FileManagerService::moveFile($document, $request->folder_id, $request->user(), $request);
        return back()->with('success', 'File moved successfully.');
    }

    public function updateCategory(Request $request, Document $document)
    {
        $request->validate(['document_category_id' => 'nullable|exists:document_categories,id']);
        $document->update(['document_category_id' => $request->document_category_id]);
        return back()->with('success', 'Category updated.');
    }

    public function destroy(Request $request, Document $document)
    {
        FileManagerService::deleteFile($document, $request->user(), $request);
        return back()->with('success', 'File moved to trash.');
    }
}
