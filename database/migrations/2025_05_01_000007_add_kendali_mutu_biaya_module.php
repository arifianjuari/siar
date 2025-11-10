<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan modul baru ke tabel modules
        DB::table('modules')->insert([
            'name' => 'Kendali Mutu Kendali Biaya',
            'description' => 'Modul untuk manajemen clinical pathway dan kendali mutu dan biaya',
            'slug' => 'kendali-mutu-biaya',
            'code' => 'KMKB',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Ambil ID modul yang baru dibuat
        $moduleId = DB::table('modules')->where('slug', 'kendali-mutu-biaya')->first()->id;

        // Dapatkan semua roles
        $roles = DB::table('roles')->get();

        // Untuk setiap role, tambahkan permissions
        foreach ($roles as $role) {
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

    public function down(): void
    {
        // Dapatkan ID modul
        $module = DB::table('modules')->where('slug', 'kendali-mutu-biaya')->first();

        if ($module) {
            // Hapus permissions untuk modul
            DB::table('role_module_permissions')->where('module_id', $module->id)->delete();

            // Hapus module dari tenant_modules (jika ada)
            DB::table('tenant_modules')->where('module_id', $module->id)->delete();

            // Hapus modul
            DB::table('modules')->where('id', $module->id)->delete();
        }
    }
};
