<?php

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('public_links')
            ->where('linkable_type', Document::class)
            ->update(['linkable_type' => 'document']);

        DB::table('public_links')
            ->where('linkable_type', Folder::class)
            ->update(['linkable_type' => 'folder']);
    }

    public function down(): void
    {
        DB::table('public_links')
            ->where('linkable_type', 'document')
            ->update(['linkable_type' => Document::class]);

        DB::table('public_links')
            ->where('linkable_type', 'folder')
            ->update(['linkable_type' => Folder::class]);
    }
};
