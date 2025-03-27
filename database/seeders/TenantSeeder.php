<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat tenant pertama
        $tenant = Tenant::create([
            'name' => 'Tenant Default',
            'domain' => 'default',
            'is_active' => true,
        ]);

        // Ambil semua modul
        $modules = Module::all();

        // Aktifkan semua modul untuk tenant ini
        foreach ($modules as $module) {
            $tenant->activateModule($module->id);
        }

        // Buat role admin
        $adminRole = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator Tenant',
            'is_active' => true,
        ]);

        // Berikan semua permission untuk role admin
        foreach ($modules as $module) {
            // Tambahkan permission untuk role ini
            RoleModulePermission::create([
                'role_id' => $adminRole->id,
                'module_id' => $module->id,
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ]);
        }

        // Buat user admin untuk tenant
        User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $adminRole->id,
            'name' => 'Admin Tenant',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->command->info('Tenant, role dan user admin berhasil dibuat');
    }
}
