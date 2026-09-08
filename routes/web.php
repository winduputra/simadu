<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\PublicLinkController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UnitKerjaController as AdminUnitKerjaController;
use App\Http\Controllers\Admin\DocumentCategoryController as AdminDocumentCategoryController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Public access routes (No Auth required)
Route::group([], function() {
    Route::get('/s/{token}', [PublicLinkController::class, 'access'])->name('public.access');
    Route::post('/s/{token}/verify', [PublicLinkController::class, 'verifyPassword'])->name('public.verify');
    Route::get('/s/{token}/stream', [PublicLinkController::class, 'stream'])->name('public.stream');
    Route::get('/s/{token}/download', [PublicLinkController::class, 'download'])->name('public.download');
    Route::get('/s/{token}/document/{document}/stream', [PublicLinkController::class, 'streamDocument'])->name('public.stream-file');
    Route::get('/s/{token}/document/{document}/download', [PublicLinkController::class, 'downloadDocument'])->name('public.download-file');
});

Route::middleware(['auth', 'active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Drive & Folders
    Route::get('/drive/{folder?}', [FolderController::class, 'index'])->name('drive.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::post('/folders/create-ajax', [FolderController::class, 'createAjax'])->name('folders.create-ajax');
    Route::put('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::post('/folders/{folder}/move', [FolderController::class, 'move'])->name('folders.move');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

    // Documents
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/stream', [DocumentController::class, 'stream'])->name('documents.stream');
    Route::put('/documents/{document}', [DocumentController::class, 'rename'])->name('documents.rename');
    Route::post('/documents/{document}/move', [DocumentController::class, 'move'])->name('documents.move');
    Route::post('/documents/{document}/category', [DocumentController::class, 'updateCategory'])->name('documents.category.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // Versions
    Route::post('/documents/{document}/versions', [VersionController::class, 'store'])->name('versions.store');
    Route::get('/documents/{document}/versions/{versi}/download', [VersionController::class, 'download'])->name('versions.download');
    Route::post('/documents/{document}/versions/{versi}/rollback', [VersionController::class, 'rollback'])->name('versions.rollback');

    // Shares
    Route::get('/shared', [ShareController::class, 'shared'])->name('shares.shared');
    Route::post('/shares', [ShareController::class, 'store'])->name('shares.store');
    Route::delete('/shares/{share}', [ShareController::class, 'destroy'])->name('shares.destroy');

    // Public Links (generating)
    Route::post('/public-links', [PublicLinkController::class, 'store'])->name('public-links.store');
    Route::delete('/public-links/{publicLink}', [PublicLinkController::class, 'revoke'])->name('public-links.revoke');

    // Trash
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/folders/{id}/restore', [TrashController::class, 'restoreFolder'])->name('trash.folders.restore');
    Route::post('/trash/documents/{id}/restore', [TrashController::class, 'restoreDocument'])->name('trash.documents.restore');
    Route::delete('/trash/folders/{id}', [TrashController::class, 'forceDeleteFolder'])->name('trash.folders.force-delete');
    Route::delete('/trash/documents/{id}', [TrashController::class, 'forceDeleteDocument'])->name('trash.documents.force-delete');
    Route::post('/trash/empty', [TrashController::class, 'emptyTrash'])->name('trash.empty');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');

    // Admin Route Group
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::resource('unit-kerja', AdminUnitKerjaController::class)->parameters([
            'unit-kerja' => 'unitKerja'
        ]);
        Route::resource('categories', AdminDocumentCategoryController::class);

        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'store'])->name('settings.store');
        Route::post('/settings/test-connection', [AdminSettingController::class, 'testConnection'])->name('settings.test-connection');
    });
});

require __DIR__.'/auth.php';
