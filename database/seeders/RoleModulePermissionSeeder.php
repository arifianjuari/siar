<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModulePermission;

class RoleModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::where('name', 'Superadmin')->first();
        $modules = Module::all();

        // Berikan full access ke Superadmin untuk semua modul
        foreach ($modules as $module) {
            RoleModulePermission::create([
                'role_id' => $superadmin->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }

        // Berikan akses view saja untuk role lainnya
        $otherRoles = Role::where('name', '!=', 'Superadmin')->get();
        foreach ($otherRoles as $role) {
            foreach ($modules as $module) {
                RoleModulePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $module->id,
                    'can_view' => true,
                    'can_create' => false,
                    'can_edit' => false,
                    'can_delete' => false,
                ]);
            }
        }
    }
}
