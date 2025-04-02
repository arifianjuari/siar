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
        $module = Module::firstOrCreate(
            ['slug' => 'work-units'],
            [
                'name' => 'Unit Kerja',
                'description' => 'Modul untuk mengelola unit kerja.',
                'icon' => 'fa-building', // Ganti dengan ikon yang sesuai
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

        // 3. Tambahkan permission default untuk role Super Admin
        // Asumsi role Super Admin memiliki ID 1 atau slug 'super-admin'
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminRole) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $superAdminRole->id, 'module_id' => $module->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                    // Tambahkan permission custom lain jika ada
                ]
            );
        }

        // Anda bisa menambahkan permission untuk role lain (misal: Admin) di sini
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            RoleModulePermission::updateOrCreate(
                ['role_id' => $adminRole->id, 'module_id' => $module->id],
                [
                    'can_view' => true,
                    'can_create' => true,
                    'can_edit' => true,
                    'can_delete' => false, // Contoh: Admin tidak bisa hapus
                ]
            );
        }

        $this->command->info('Work Unit module seeded successfully.');
    }
}
