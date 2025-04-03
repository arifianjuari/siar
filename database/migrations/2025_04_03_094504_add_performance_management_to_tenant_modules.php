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
        // Dapatkan ID modul performance management
        $performanceManagementModule = DB::table('modules')
            ->where('slug', 'performance-management')
            ->first();

        if ($performanceManagementModule) {
            $moduleId = $performanceManagementModule->id;

            // Tambahkan modul ke tenant_modules untuk semua tenant
            $tenants = DB::table('tenants')->get();
            foreach ($tenants as $tenant) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $moduleId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Tambahkan izin modul ke semua peran admin
            $roles = DB::table('roles')
                ->whereIn('slug', ['superadmin', 'admin'])
                ->get();

            foreach ($roles as $role) {
                DB::table('role_module_permissions')->insert([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dapatkan ID modul performance management
        $performanceManagementModule = DB::table('modules')
            ->where('slug', 'performance-management')
            ->first();

        if ($performanceManagementModule) {
            $moduleId = $performanceManagementModule->id;

            // Hapus dari tenant_modules
            DB::table('tenant_modules')
                ->where('module_id', $moduleId)
                ->delete();

            // Hapus dari role_module_permissions
            DB::table('role_module_permissions')
                ->where('module_id', $moduleId)
                ->delete();
        }
    }
};
