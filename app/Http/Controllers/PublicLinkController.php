<?php

namespace App\Http\Controllers;

use App\Models\PublicLink;
use App\Models\Document;
use App\Models\Folder;
use App\Services\PublicLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PublicLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'linkable_type' => 'required|in:folder,document',
            'linkable_id' => 'required|integer',
            'permission' => 'required|in:viewer,editor',
            'password' => 'nullable|string|min:4',
            'expires_at' => 'nullable|date|after:now',
            'max_access_count' => 'nullable|integer|min:1',
        ]);

        $item = $request->linkable_type === 'folder'
            ? Folder::findOrFail($request->linkable_id)
            : Document::findOrFail($request->linkable_id);

        $link = PublicLinkService::createLink(
            $item,
            $request->user(),
            $request->permission,
            $request->password,
            $request->expires_at ? Carbon::parse($request->expires_at) : null,
            $request->max_access_count,
            $request
        );

        return back()->with('success', 'Public link created.')->with('public_link_url', route('public.access', $link->token));
    }

    public function revoke(Request $request, PublicLink $publicLink)
    {
        PublicLinkService::revokeLink($publicLink, $request->user(), $request);
        return back()->with('success', 'Public link revoked.');
    }

    // Public access (no auth required)
    public function access(Request $request, string $token)
    {
        $link = PublicLinkService::findByToken($token);

        if (!$link || !$link->isAccessible()) {
            abort(404, 'This link is no longer available.');
        }

        if ($link->has_password && !session('public_link_verified_' . $link->id)) {
            return view('public-link.password', compact('link', 'token'));
        }

        $link->incrementAccess();

        return view('public-link.access', compact('link'));
    }

    public function verifyPassword(Request $request, string $token)
    {
        $link = PublicLinkService::findByToken($token);
        if (!$link || !$link->isAccessible()) {
            abort(404);
        }

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $link->getRawOriginal('password'))) {
            return back()->with('error', 'Incorrect password.');
        }

        session(['public_link_verified_' . $link->id => true]);
        return redirect()->route('public.access', $token);
    }

    public function download(string $token)
    {
        $link = PublicLinkService::findByToken($token);
        if (!$link || !$link->isAccessible() || $link->permission === 'viewer') {
            abort(403);
        }

        $item = $link->linkable;
        if (!($item instanceof Document)) {
            abort(400, 'Cannot download a folder.');
        }

        $disk = \App\Services\FileManagerService::getNasDisk();
        return Storage::disk($disk)->download($item->storage_path, $item->nama_file_asli);
    }

    public function stream(string $token)
    {
        $link = PublicLinkService::findByToken($token);
        if (!$link || !$link->isAccessible()) {
            abort(403);
        }

        if ($link->has_password && !session('public_link_verified_' . $link->id)) {
            abort(403);
        }

        $item = $link->linkable;
        if (!($item instanceof Document)) {
            abort(400, 'Cannot preview a folder.');
        }

        return $this->streamFile($item);
    }

    public function downloadDocument(string $token, Document $document)
    {
        $link = PublicLinkService::findByToken($token);
        if (!$link || !$link->isAccessible() || $link->permission === 'viewer') {
            abort(403);
        }

        $item = $link->linkable;
        if ($item instanceof Folder) {
            $isChild = $document->folder_id === $item->id || in_array($item->id, array_column($document->folder?->ancestors() ?? [], 'id'));
            if (!$isChild) {
                abort(403, 'Document does not belong to this shared folder.');
            }
        } elseif ($item instanceof Document) {
            if ($item->id !== $document->id) {
                abort(403);
            }
        }

        $disk = \App\Services\FileManagerService::getNasDisk();
        return Storage::disk($disk)->download($document->storage_path, $document->nama_file_asli);
    }

    public function streamDocument(string $token, Document $document)
    {
        $link = PublicLinkService::findByToken($token);
        if (!$link || !$link->isAccessible()) {
            abort(403);
        }

        if ($link->has_password && !session('public_link_verified_' . $link->id)) {
            abort(403);
        }

        $item = $link->linkable;
        if (!($item instanceof Folder)) {
            abort(403);
        }

        $isChild = $document->folder_id === $item->id || in_array($item->id, array_column($document->folder?->ancestors() ?? [], 'id'));
        if (!$isChild) {
            abort(403, 'Document does not belong to this shared folder.');
        }

        return $this->streamFile($document);
    }

    private function streamFile(Document $document)
    {
        $disk = \App\Services\FileManagerService::getNasDisk();
        if (!Storage::disk($disk)->exists($document->storage_path)) {
            abort(404, 'File not found on storage.');
        }

        return Storage::disk($disk)->response($document->storage_path, $document->nama_file_asli, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . addslashes($document->nama_file_asli) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
