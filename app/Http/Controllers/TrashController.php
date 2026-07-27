<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Services\FileManagerService;
use App\Services\FolderService;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $folders = Folder::onlyTrashed()->where('user_id', $user->id)->latest('deleted_at')->get();
        $documents = Document::onlyTrashed()->where('user_id', $user->id)->latest('deleted_at')->get();
        return view('drive.trash', compact('folders', 'documents', 'user'));
    }

    public function restoreFolder(Request $request, int $id)
    {
        $folder = Folder::onlyTrashed()->findOrFail($id);
        FolderService::restore($folder, $request->user(), $request);
        return back()->with('success', 'Folder restored.');
    }

    public function restoreDocument(Request $request, int $id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        FileManagerService::restoreFile($document, $request->user(), $request);
        return back()->with('success', 'File restored.');
    }

    public function forceDeleteFolder(Request $request, int $id)
    {
        $folder = Folder::onlyTrashed()->findOrFail($id);
        FolderService::forceDelete($folder, $request->user(), $request);
        return back()->with('success', 'Folder permanently deleted.');
    }

    public function forceDeleteDocument(Request $request, int $id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        FileManagerService::forceDeleteFile($document, $request->user(), $request);
        return back()->with('success', 'File permanently deleted.');
    }

    public function emptyTrash(Request $request)
    {
        $user = $request->user();
        
        $folders = Folder::onlyTrashed()->where('user_id', $user->id)->get();
        foreach ($folders as $folder) {
            if ($folder->exists) {
                FolderService::forceDelete($folder, $user, $request);
            }
        }

        $documents = Document::onlyTrashed()->where('user_id', $user->id)->get();
        foreach ($documents as $doc) {
            if ($doc->exists) {
                FileManagerService::forceDeleteFile($doc, $user, $request);
            }
        }

        return back()->with('success', 'Trash emptied.');
    }
}
