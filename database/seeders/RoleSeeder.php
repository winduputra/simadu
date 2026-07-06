<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nama' => 'Super Admin', 'slug' => 'super-admin', 'deskripsi' => 'Full system access'],
            ['nama' => 'Admin Unit', 'slug' => 'admin-unit', 'deskripsi' => 'Unit-level administration'],
            ['nama' => 'User', 'slug' => 'user', 'deskripsi' => 'Standard user access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
