<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SPOManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah modul SPO Management sudah ada
        $module = Module::where('name', 'Manajemen SPO')
            ->orWhere('slug', 'spo-management')
            ->first();

        if (!$module) {
            // Buat modul SPO Management
            $module = Module::create([
                'name' => 'Manajemen SPO',
                'code' => 'spo-management',
                'slug' => 'spo-management',
                'description' => 'Modul untuk mengelola Standar Prosedur Operasional (SPO) di rumah sakit',
                'icon' => 'file-text',
                'is_active' => true,
                'order' => 7, // Sesuaikan dengan urutan yang diinginkan
            ]);

            $this->command->info('Modul Manajemen SPO berhasil dibuat.');
        } else {
            $this->command->info('Modul Manajemen SPO sudah ada. Melanjutkan pengaturan izin...');
        }

        // Tambahkan izin untuk modul
        $roles = Role::all();
        foreach ($roles as $role) {
            // Admin, superadmin, dan manager bisa mengelola penuh
            $canManage = in_array($role->slug, ['admin', 'superadmin', 'manager']);
            $canCreate = $canManage;
            $canEdit = $canManage;
            $canDelete = $canManage;

            // Semua peran bisa melihat
            $canView = true;

            // Cek jika izin sudah ada
            $permissionExists = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$permissionExists) {
                DB::table('role_module_permissions')->insert([
                    'role_id' => $role->id,
                    'module_id' => $module->id,
                    'can_view' => $canView,
                    'can_create' => $canCreate,
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete,
                    'can_manage' => $canManage,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Izin modul Manajemen SPO berhasil diatur.');

        // Tambahkan modul ke tenant yang ada
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Cek jika tenant sudah memiliki modul
            $tenantHasModule = DB::table('tenant_modules')
                ->where('tenant_id', $tenant->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$tenantHasModule) {
                DB::table('tenant_modules')->insert([
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Modul Manajemen SPO berhasil ditambahkan ke semua tenant.');
    }
}
