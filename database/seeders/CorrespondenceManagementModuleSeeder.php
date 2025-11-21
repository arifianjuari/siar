<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CorrespondenceManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan modul Correspondence Management
        $module = Module::where('slug', 'correspondence-management')->first();

        if (!$module) {
            $this->command->info('Modul Correspondence Management tidak ditemukan. Membuat modul baru...');
            $module = Module::create([
                'name' => 'Korespondensi',
                'slug' => 'correspondence-management',
                'description' => 'Modul untuk mengelola surat-menyurat',
                'icon' => 'fa-envelope',
                'code' => 'CORRES',
                'order' => 4,
                'is_active' => true
            ]);
            $this->command->info('Modul Correspondence Management berhasil dibuat!');
        } else {
            $this->command->info('Modul Correspondence Management sudah ada, memastikan kode dan slug sesuai');
            // Update jika perlu
            $module->update([
                'name' => 'Korespondensi',
                'icon' => 'fa-envelope',
                'code' => 'CORRES',
                'is_active' => true
            ]);
        }

        // 2. Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Aktifkan modul untuk tenant ini
            $tenant->modules()->syncWithoutDetaching([$module->id => ['is_active' => true]]);
            $this->command->info("Modul Correspondence Management diaktifkan untuk tenant: " . $tenant->name);

            // 3. Berikan akses ke semua role di tenant ini
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                RoleModulePermission::updateOrCreate(
                    ['role_id' => $role->id, 'module_id' => $module->id],
                    [
                        'can_view' => true,
                        'can_create' => $role->slug != 'staf', // Semua role kecuali staff dapat membuat
                        'can_edit' => $role->slug != 'staf', // Semua role kecuali staff dapat mengedit
                        'can_delete' => in_array($role->slug, ['super-admin', 'tenant-admin']), // Hanya admin yang dapat menghapus
                    ]
                );
                $this->command->info("Hak akses diberikan kepada role {$role->name} di tenant {$tenant->name}");
            }
        }
    }
}
