<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::where('slug', 'super-admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@simadu.local'],
            [
                'role_id' => $superAdmin->id,
                'nip' => '199000001000001',
                'nama' => 'Super Admin',
                'password' => 'password',
                'is_active' => true,
                'storage_quota' => 0,
            ]
        );

        // Default system settings
        SystemSetting::set('nas_path', storage_path('app/documents'));
        SystemSetting::set('default_storage_quota', '0');
    }
}
