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

        if ($folder) {
            $hasAccess = $folder->user_id === $user->id || $user->isSuperAdmin() || \App\Services\ShareService::getPermission($folder, $user) !== null;
            if (!$hasAccess) {
                foreach ($folder->ancestors() as $ancestor) {
                    if (\App\Services\ShareService::getPermission($ancestor, $user) !== null) {
                        $hasAccess = true;
                        break;
                    }
                }
            }
            if (!$hasAccess) {
                abort(403, 'You do not have access to this folder.');
            }

            $folders = Folder::where('parent_id', $folder->id)->orderBy('is_pinned', 'desc')->orderBy('nama')->get();
            $documents = Document::where('folder_id', $folder->id)->orderBy('is_pinned', 'desc')->orderBy('nama')->get();
        } else {
            $folders = Folder::where('user_id', $user->id)->whereNull('parent_id')->orderBy('is_pinned', 'desc')->orderBy('nama')->get();
            $documents = Document::where('user_id', $user->id)->whereNull('folder_id')->orderBy('is_pinned', 'desc')->orderBy('nama')->get();
        }

        $breadcrumbs = $folder ? array_merge($folder->ancestors(), [$folder]) : [];

        $users = \App\Models\User::where('id', '!=', $user->id)->orderBy('nama')->get();
        $unitKerjas = \App\Models\UnitKerja::orderBy('nama')->get();

        return view('drive.index', compact('folders', 'documents', 'folder', 'breadcrumbs', 'user', 'users', 'unitKerjas'));
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

    /**
     * Membuat folder via AJAX dan mengembalikan folder_id (digunakan saat drag & drop folder).
     */
    public function createAjax(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = FolderService::create([
            'nama' => $request->nama,
            'parent_id' => $request->parent_id,
        ], $request->user(), $request);

        return response()->json([
            'id' => $folder->id,
            'nama' => $folder->nama,
        ]);
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
