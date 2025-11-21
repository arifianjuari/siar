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
                'code' => 'KMKB',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Ambil ID modul
        $module = DB::table('modules')->where('slug', 'kendali-mutu-biaya')->first();
        if (!$module) {
            return; // Skip jika modul tidak ditemukan
        }
        
        $moduleId = $module->id;

        // Dapatkan semua roles
        $roles = DB::table('roles')->get();

        // Untuk setiap role, tambahkan permissions menggunakan struktur boolean
        foreach ($roles as $role) {
            // Cek apakah permission sudah ada
            $permissionExists = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module_id', $moduleId)
                ->exists();

            if (!$permissionExists) {
                // Berikan semua permission ke superadmin
                if ($role->name === 'Super Admin') {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                // Berikan permission view ke admin
                elseif ($role->name === 'Admin') {
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $moduleId,
                        'can_view' => true,
                        'can_create' => false,
                        'can_edit' => false,
                        'can_delete' => false,
                        'can_export' => false,
                        'can_import' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // No rollback needed for module permissions update
    }
};
