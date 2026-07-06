<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Share;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\ShareService;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function shared(Request $request)
    {
        $user = $request->user();

        // Items shared directly to user
        $directShares = Share::where('shared_to_type', User::class)
            ->where('shared_to_id', $user->id)
            ->with(['shareable', 'sharedBy'])
            ->latest()
            ->get();

        // Items shared to user's unit
        $unitShares = collect();
        if ($user->unit_kerja_id) {
            $unitShares = Share::where('shared_to_type', UnitKerja::class)
                ->where('shared_to_id', $user->unit_kerja_id)
                ->with(['shareable', 'sharedBy'])
                ->latest()
                ->get();
        }

        $shares = $directShares->merge($unitShares)->unique('id');

        return view('drive.shared', compact('shares', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shareable_type' => 'required|in:folder,document',
            'shareable_id' => 'required|integer',
            'shared_to_type' => 'required|in:user,unit',
            'shared_to_id' => 'required|integer',
            'permission' => 'required|in:viewer,editor,manager',
        ]);

        $item = $request->shareable_type === 'folder'
            ? Folder::findOrFail($request->shareable_id)
            : Document::findOrFail($request->shareable_id);

        $recipient = $request->shared_to_type === 'user'
            ? User::findOrFail($request->shared_to_id)
            : UnitKerja::findOrFail($request->shared_to_id);

        ShareService::shareItem($item, $recipient, $request->permission, $request->user(), $request);

        return back()->with('success', 'Item shared successfully.');
    }

    public function destroy(Request $request, Share $share)
    {
        $item = $share->shareable;
        $recipient = $share->sharedTo;
        ShareService::unshareItem($item, $recipient, $request->user(), $request);
        return back()->with('success', 'Share removed.');
    }
}
