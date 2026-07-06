<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\SystemSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dynamic configuration of the 'nas' disk using settings in DB (if the table exists)
        try {
            if (Schema::hasTable('system_settings')) {
                $nasPath = SystemSetting::get('nas_path');
                if ($nasPath) {
                    config(['filesystems.disks.nas.root' => $nasPath]);
                }
            }
        } catch (\Exception $e) {
            // Table doesn't exist yet (e.g. during migrations)
        }

        // Polymorphic morph map
        Relation::morphMap([
            'folder' => \App\Models\Folder::class,
            'document' => \App\Models\Document::class,
            'user' => \App\Models\User::class,
            'unit' => \App\Models\UnitKerja::class,
        ]);
    }
}
