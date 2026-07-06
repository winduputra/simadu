<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $nasPath = SystemSetting::get('nas_path', storage_path('app/documents'));
        $defaultQuota = SystemSetting::get('default_storage_quota', '0');

        return view('admin.settings', compact('nasPath', 'defaultQuota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nas_path' => 'required|string',
            'default_storage_quota' => 'required|integer|min:0',
        ]);

        SystemSetting::set('nas_path', $request->nas_path);
        SystemSetting::set('default_storage_quota', $request->default_storage_quota);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testConnection(Request $request)
    {
        $request->validate([
            'nas_path' => 'required|string',
        ]);

        $path = $request->nas_path;

        try {
            if (!file_exists($path)) {
                // Try creating directory to see if writable
                if (!@mkdir($path, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Directory does not exist and could not be created. Check permissions.',
                    ]);
                }
            }

            if (!is_writable($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Directory exists but is NOT writable.',
                ]);
            }

            // Test writing a temp file
            $testFile = $path . DIRECTORY_SEPARATOR . 'simadu_test_connection.txt';
            if (@file_put_contents($testFile, 'SIMADU Connection Test - ' . now()) === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to write test file to directory.',
                ]);
            }

            // Test reading it
            $content = @file_get_contents($testFile);
            if (empty($content)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read test file from directory.',
                ]);
            }

            // Delete test file
            @unlink($testFile);

            return response()->json([
                'success' => true,
                'message' => 'Connection test successful! Path exists, is writable, and supports read/write.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
