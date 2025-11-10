<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek modul activity-management sudah ada atau belum
        $module = Module::where('slug', 'activity-management')->first();

        if (!$module) {
            // Jika belum ada, buat baru
            $module = Module::create([
                'name' => 'Pengelolaan Kegiatan',
                'code' => 'activity-management',
                'slug' => 'activity-management',
                'description' => 'Modul untuk mengelola seluruh kegiatan dan tindak lanjut dari berbagai modul lainnya',
                'icon' => 'fa-tasks',
                'order' => 5,
                'is_active' => true,
            ]);
        }

        // Tambahkan izin untuk semua role superadmin
        $superadminRole = Role::where('name', 'Superadmin')->first();

        if ($superadminRole) {
            // Cek apakah sudah ada izin untuk modul ini
            $exists = RoleModulePermission::where('role_id', $superadminRole->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$exists) {
                // Tambahkan semua permission untuk Superadmin
                RoleModulePermission::create([
                    'role_id' => $superadminRole->id,
                    'module_id' => $module->id,
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    'can_export' => true,
                    'can_import' => true,
                    'can_generate_reports' => true,
                ]);
            }
        }

        // Tambahkan juga ke modul yang diaktifkan untuk tenant "superadmin"
        $superadminTenant = DB::table('tenants')->where('domain', 'superadmin')->first();

        if ($superadminTenant) {
            $exists = DB::table('tenant_modules')
                ->where('tenant_id', $superadminTenant->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$exists) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $superadminTenant->id,
                    'module_id' => $module->id,
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
        // Cari modul activity-management
        $module = Module::where('slug', 'activity-management')->first();

        if ($module) {
            // Hapus izin untuk role superadmin
            $superadminRole = Role::where('name', 'Superadmin')->first();

            if ($superadminRole) {
                RoleModulePermission::where('role_id', $superadminRole->id)
                    ->where('module_id', $module->id)
                    ->delete();
            }

            // Hapus dari tenant superadmin
            $superadminTenant = DB::table('tenants')->where('domain', 'superadmin')->first();

            if ($superadminTenant) {
                DB::table('tenant_modules')
                    ->where('tenant_id', $superadminTenant->id)
                    ->where('module_id', $module->id)
                    ->delete();
            }
        }
    }
};
