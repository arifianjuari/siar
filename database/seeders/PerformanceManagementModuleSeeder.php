<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class PerformanceManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan modul Performance Management
        $module = Module::where('slug', 'performance-management')->first();

        if (!$module) {
            $this->command->info('Modul Performance Management tidak ditemukan. Membuat modul baru...');
            $module = Module::create([
                'name' => 'KPI',
                'slug' => 'performance-management',
                'description' => 'Modul untuk mengelola kinerja dan KPI',
                'icon' => 'fa-chart-line',
                'code' => 'PERF',
                'order' => 5,
                'is_active' => true
            ]);
            $this->command->info('Modul Performance Management berhasil dibuat!');
        } else {
            $this->command->info('Modul Performance Management sudah ada, memastikan kode dan slug sesuai');
            // Update jika perlu
            $module->update([
                'name' => 'KPI',
                'icon' => 'fa-chart-line',
                'code' => 'PERF',
                'is_active' => true
            ]);
        }

        // 2. Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Aktifkan modul untuk tenant ini
            $tenant->modules()->syncWithoutDetaching([$module->id => ['is_active' => true]]);
            $this->command->info("Modul Performance Management diaktifkan untuk tenant: " . $tenant->name);

            // 3. Berikan akses ke semua role di tenant ini
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    [
                        'can_view' => true,
                        'can_create' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manajemen-strategis', 'manajemen-eksekutif']),
                        'can_edit' => in_array($role->slug, ['super-admin', 'tenant-admin', 'manajemen-strategis', 'manajemen-eksekutif']),
                        'can_delete' => in_array($role->slug, ['super-admin', 'tenant-admin']),
                    ]
                );
                $this->command->info("Hak akses diberikan kepada role {$role->name} di tenant {$tenant->name}");
            }
        }
    }
}
