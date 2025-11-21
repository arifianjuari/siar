<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah modul SPO Management sudah ada
        $moduleExists = DB::table('modules')
            ->where('name', 'Manajemen SPO')
            ->orWhere('slug', 'spo-management')
            ->exists();

        if (!$moduleExists) {
            // Tambahkan modul baru
            $moduleId = DB::table('modules')->insertGetId([
                'name' => 'Manajemen SPO',
                'code' => 'spo-management',
                'slug' => 'spo-management',
                'description' => 'Modul untuk mengelola Standar Prosedur Operasional (SPO) di rumah sakit',
                'icon' => 'file-text',
                'is_active' => true,
                'order' => 7, // Sesuaikan dengan urutan yang diinginkan
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Tambahkan izin untuk modul baru
            $roles = DB::table('roles')->get();
            foreach ($roles as $role) {
                // Hanya admin, superadmin, dan manager yang bisa mengelola penuh
                $canManage = in_array($role->slug, ['admin', 'superadmin', 'manager']);
                $canCreate = $canManage;
                $canEdit = $canManage;
                $canDelete = $canManage;

                // Semua peran bisa melihat
                $canView = true;

                DB::table('role_module_permissions')->insert([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_view' => $canView,
                    'can_create' => $canCreate,
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete,
                    'can_export' => false,
                    'can_import' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Tambahkan modul ke semua tenant yang sudah ada
            $tenants = DB::table('tenants')->get();
            foreach ($tenants as $tenant) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $moduleId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cari ID modul SPO Management
        $moduleId = DB::table('modules')
            ->where('name', 'Manajemen SPO')
            ->orWhere('slug', 'spo-management')
            ->value('id');

        if ($moduleId) {
            // Hapus izin modul
            DB::table('role_module_permissions')
                ->where('module_id', $moduleId)
                ->delete();

            // Hapus modul dari tenant
            DB::table('tenant_modules')
                ->where('module_id', $moduleId)
                ->delete();

            // Hapus modul
            DB::table('modules')
                ->where('id', $moduleId)
                ->delete();
        }
    }
};
