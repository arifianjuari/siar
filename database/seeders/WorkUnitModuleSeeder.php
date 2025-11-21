<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkUnitModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tambahkan modul ke tabel modules
        $module = Module::updateOrCreate(
            ['slug' => 'work-units'],
            [
                'name' => 'Unit Kerja',
                'description' => 'Modul untuk mengelola unit kerja.',
                'icon' => 'archive', // Menggunakan icon feather
                'code' => 'WORKUNIT',
                'order' => 99, // Sesuaikan dengan urutan yang diinginkan
                'is_active' => true
            ]
        );

        // 2. Aktifkan modul untuk semua tenant yang ada (atau tenant tertentu)
        $tenants = Tenant::all(); // Anda bisa filter tenant jika perlu
        foreach ($tenants as $tenant) {
            DB::table('tenant_modules')->updateOrInsert(
                ['tenant_id' => $tenant->id, 'module_id' => $module->id],
                ['is_active' => true]
            );
        }

        // 3. Tambahkan permission default untuk berbagai role
        // Role Superadmin
        $superAdminRole = Role::where('slug', 'superadmin')->first();
        if ($superAdminRole) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $superAdminRole->id, 'module_id' => $module->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                ]
            );
        }

        // Role Tenant Admin
        $tenantAdminRoles = Role::where('slug', 'tenant-admin')->get();
        foreach ($tenantAdminRoles as $adminRole) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $adminRole->id, 'module_id' => $module->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                ]
            );
        }

        // Role Admin reguler
        $adminRoles = Role::where('slug', 'admin')->get();
        foreach ($adminRoles as $adminRole) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $adminRole->id, 'module_id' => $module->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => false, // Admin reguler tidak bisa hapus
                ]
            );
        }

        // 4. Pastikan rute telah diatur dengan benar di sidebar
        // Sidebar menggunakan rute tenant.work-units.* untuk akses ke modul ini

        $this->command->info('Modul Work Unit berhasil diseeded.');
    }
}
