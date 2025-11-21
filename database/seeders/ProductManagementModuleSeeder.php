<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ProductManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan atau buat modul Product Management
        $module = Module::where('code', 'product-management')->first();

        if (!$module) {
            $this->command->info('Modul Product Management tidak ditemukan. Membuat modul baru...');
            $module = Module::create([
                'name' => 'Manajemen Produk',
                'code' => 'product-management',
                'description' => 'Modul untuk mengelola produk dan inventori',
                'icon' => 'fa-box',
                'order' => 4,
                'is_active' => true
            ]);
            $this->command->info('Modul Product Management berhasil dibuat!');
        } else {
            $this->command->info('Modul Product Management sudah ada, memastikan konfigurasi sesuai');
            // Update jika perlu
            $module->update([
                'name' => 'Manajemen Produk',
                'description' => 'Modul untuk mengelola produk dan inventori',
                'icon' => 'fa-box',
                'is_active' => true
            ]);
        }

        // 2. Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Aktifkan modul untuk tenant ini
            $tenant->modules()->syncWithoutDetaching([$module->id => ['is_active' => true]]);
            $this->command->info("Modul Product Management diaktifkan untuk tenant: " . $tenant->name);

            // 3. Berikan akses ke semua role di tenant ini
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    [
                        'can_view' => true,
                        'can_create' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                        'can_edit' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                        'can_delete' => in_array($role->slug, ['super-admin', 'tenant-admin']),
                        'can_export' => true,
                        'can_import' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manager']),
                    ]
                );
                $this->command->info("Hak akses diberikan kepada role {$role->name} di tenant {$tenant->name}");
            }
        }

        $this->command->info('ProductManagementModuleSeeder selesai!');
    }
}
