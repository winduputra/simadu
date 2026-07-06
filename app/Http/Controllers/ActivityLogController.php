<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ActivityLog::with('user', 'loggable')->latest('created_at');

        // Non-super-admin only sees their own logs
        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        $logs = $query->paginate(50);

        return view('activity.index', compact('logs', 'user'));
    }
}
