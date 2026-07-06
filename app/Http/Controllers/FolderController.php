<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Share;
use App\Services\FolderService;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function index(Request $request, ?Folder $folder = null)
    {
        $user = $request->user();

        $query = Folder::where('user_id', $user->id);
        if ($folder) {
            $query->where('parent_id', $folder->id);
        } else {
            $query->whereNull('parent_id');
        }
        $folders = $query->orderBy('is_pinned', 'desc')->orderBy('nama')->get();

        $docQuery = Document::where('user_id', $user->id);
        if ($folder) {
            $docQuery->where('folder_id', $folder->id);
        } else {
            $docQuery->whereNull('folder_id');
        }
        $documents = $docQuery->orderBy('is_pinned', 'desc')->orderBy('nama')->get();

        $breadcrumbs = $folder ? array_merge($folder->ancestors(), [$folder]) : [];

        return view('drive.index', compact('folders', 'documents', 'folder', 'breadcrumbs', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'color' => 'nullable|string|max:7',
        ]);

        FolderService::create($request->only(['nama', 'parent_id', 'unit_kerja_id', 'color']), $request->user(), $request);

        return back()->with('success', 'Folder created successfully.');
    }

    public function update(Request $request, Folder $folder)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        FolderService::rename($folder, $request->nama, $request->user(), $request);
        return back()->with('success', 'Folder renamed successfully.');
    }

    public function move(Request $request, Folder $folder)
    {
        $request->validate(['parent_id' => 'nullable|exists:folders,id']);
        FolderService::move($folder, $request->parent_id, $request->user(), $request);
        return back()->with('success', 'Folder moved successfully.');
    }

    public function destroy(Request $request, Folder $folder)
    {
        FolderService::delete($folder, $request->user(), $request);
        return back()->with('success', 'Folder moved to trash.');
    }
}
