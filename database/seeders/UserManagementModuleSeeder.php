<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\TenantModule;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\RoleModulePermission;

class UserManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Periksa apakah modul User Management sudah ada
        $module = Module::where('code', 'user_management')
            ->orWhere('slug', 'user-management')
            ->first();

        if (!$module) {
            // Buat modul baru jika belum ada
            $module = new Module();
            $module->name = 'Kelola Pengguna';
            $module->description = 'Modul untuk mengelola pengguna dan peran dalam sistem';
            $module->code = 'user_management';
            $module->slug = 'user-management';
            $module->icon = 'fas fa-users';
            $module->is_active = true;
            $module->save();

            echo "Modul User Management berhasil dibuat!\n";
        } else {
            // Update data modul yang sudah ada
            $module->name = 'Kelola Pengguna';
            $module->description = 'Modul untuk mengelola pengguna dan peran dalam sistem';
            $module->code = 'user_management';
            $module->slug = 'user-management';
            $module->icon = 'fas fa-users';
            $module->is_active = true;
            $module->save();

            echo "Modul User Management sudah ada, data diperbarui\n";
        }

        // Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            $tenantModule = TenantModule::where('tenant_id', $tenant->id)
                ->where('module_id', $module->id)
                ->first();

            if (!$tenantModule) {
                $tenantModule = new TenantModule();
                $tenantModule->tenant_id = $tenant->id;
                $tenantModule->module_id = $module->id;
                $tenantModule->is_active = true;
                $tenantModule->save();

                echo "Modul User Management diaktifkan untuk tenant: {$tenant->name}\n";
            } else {
                $tenantModule->is_active = true;
                $tenantModule->save();

                echo "Modul User Management sudah aktif untuk tenant: {$tenant->name}\n";
            }

            // Berikan akses ke semua role
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                // Periksa apakah sudah ada izin untuk role dan modul ini
                $permission = RoleModulePermission::where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->first();

                if (!$permission) {
                    $permission = new RoleModulePermission();
                    $permission->role_id = $role->id;
                    $permission->module_id = $module->id;
                    $permission->can_view = true;
                    $permission->can_create = true;
                    $permission->can_edit = true;
                    $permission->can_delete = true;
                    $permission->can_export = true;
                    $permission->can_import = true;
                    $permission->save();

                    echo "Hak akses diberikan kepada role {$role->name} di tenant {$tenant->name}\n";
                } else {
                    $permission->can_view = true;
                    $permission->can_create = true;
                    $permission->can_edit = true;
                    $permission->can_delete = true;
                    $permission->can_export = true;
                    $permission->can_import = true;
                    $permission->save();

                    echo "Hak akses diperbarui untuk role {$role->name} di tenant {$tenant->name}\n";
                }
            }
        }
    }
}
