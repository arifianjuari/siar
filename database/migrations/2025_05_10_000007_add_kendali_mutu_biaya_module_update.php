<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah modul sudah ada
        $moduleExists = DB::table('modules')->where('slug', 'kendali-mutu-biaya')->exists();

        if (!$moduleExists) {
            // Tambahkan modul baru ke tabel modules
            DB::table('modules')->insert([
                'name' => 'Kendali Mutu Kendali Biaya',
                'description' => 'Modul untuk manajemen clinical pathway dan kendali mutu dan biaya',
                'slug' => 'kendali-mutu-biaya',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Ambil ID modul
        $moduleId = DB::table('modules')->where('slug', 'kendali-mutu-biaya')->first()->id;

        // Tambahkan permissions untuk modul
        $permissions = [
            'view_clinical_pathway',
            'create_clinical_pathway',
            'edit_clinical_pathway',
            'delete_clinical_pathway',
            'view_cp_evaluation',
            'create_cp_evaluation',
            'edit_cp_evaluation',
            'delete_cp_evaluation',
            'view_cp_tariff',
            'create_cp_tariff',
            'edit_cp_tariff',
            'delete_cp_tariff'
        ];

        $roleModulePermissions = [];

        // Dapatkan semua roles
        $roles = DB::table('roles')->get();

        // Untuk setiap role, tambahkan permissions
        foreach ($roles as $role) {
            foreach ($permissions as $permission) {
                // Cek apakah permission sudah ada
                $permissionExists = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module_id', $moduleId)
                    ->where('permission', $permission)
                    ->exists();

                if (!$permissionExists) {
                    // Berikan semua permission ke superadmin
                    if ($role->name === 'Super Admin') {
                        $roleModulePermissions[] = [
                            'role_id' => $role->id,
                            'module_id' => $moduleId,
                            'permission' => $permission,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    // Berikan permission view ke admin
                    elseif ($role->name === 'Admin' && str_starts_with($permission, 'view_')) {
                        $roleModulePermissions[] = [
                            'role_id' => $role->id,
                            'module_id' => $moduleId,
                            'permission' => $permission,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }

        // Insert permissions yang belum ada
        if (!empty($roleModulePermissions)) {
            DB::table('role_module_permissions')->insert($roleModulePermissions);
        }
    }

    public function down(): void
    {
        // No rollback needed for module permissions update
    }
};
