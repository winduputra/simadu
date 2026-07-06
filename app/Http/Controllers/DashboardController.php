<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $recentDocuments = Document::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $totalDocuments = Document::where('user_id', $user->id)->count();
        $totalFolders = Folder::where('user_id', $user->id)->count();

        return view('dashboard', compact('recentDocuments', 'totalDocuments', 'totalFolders', 'user'));
    }
}
