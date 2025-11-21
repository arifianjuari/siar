<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiskManagementModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah modul risk-management sudah ada
        $module = Module::where('slug', 'risk-management')
            ->orWhere('code', 'risk-management')
            ->first();

        // Jika belum ada, buat modul baru
        if (!$module) {
            $module = Module::create([
                'name' => 'Manajemen Risiko',
                'description' => 'Modul untuk mengelola laporan risiko dan insiden',
                'code' => 'risk-management',
                'slug' => 'risk-management',
                'icon' => 'fa-exclamation-triangle',
                'is_active' => true,
                'order' => 5, // Sesuaikan dengan kebutuhan
            ]);

            $this->command->info('Modul Manajemen Risiko berhasil dibuat');
        } else {
            // Pastikan kode dan slug sesuai
            $module->code = 'risk-management';
            $module->slug = 'risk-management';
            $module->save();

            $this->command->info('Modul Manajemen Risiko sudah ada, memastikan kode dan slug sesuai');
        }

        // Aktifkan modul untuk semua tenant
        $tenants = Tenant::all();
        foreach ($tenants as $tenant) {
            // Cek apakah tenant sudah memiliki modul ini
            $existingModule = $tenant->modules()
                ->where('modules.id', $module->id)
                ->first();

            if (!$existingModule) {
                // Tambahkan modul ke tenant
                $tenant->modules()->attach($module->id, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->command->info("Modul Manajemen Risiko diaktifkan untuk tenant: {$tenant->name}");
            } else {
                // Pastikan modul aktif
                if (!$existingModule->pivot->is_active) {
                    $tenant->modules()->updateExistingPivot($module->id, [
                        'is_active' => true,
                        'updated_at' => now()
                    ]);

                    $this->command->info("Modul Manajemen Risiko diaktifkan ulang untuk tenant: {$tenant->name}");
                }
            }

            // Berikan akses ke semua role dalam tenant
            $roles = Role::where('tenant_id', $tenant->id)->get();
            foreach ($roles as $role) {
                // Cek apakah role sudah memiliki akses ke modul ini
                $existingPermission = DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->first();

                if (!$existingPermission) {
                    // Tambahkan permission
                    DB::table('role_module_permissions')->insert([
                        'role_id' => $role->id,
                        'module_id' => $module->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $this->command->info("Hak akses diberikan kepada role {$role->name} di tenant {$tenant->name}");
                } else {
                    // Update permission yang sudah ada
                    DB::table('role_module_permissions')
                        ->where('role_id', $role->id)
                        ->where('module_id', $module->id)
                        ->update([
                            'can_view' => true,
                            'can_create' => true,
                            'can_edit' => true,
                            'can_delete' => true,
                            'can_export' => true,
                            'can_import' => true,
                            'updated_at' => now()
                        ]);

                    $this->command->info("Hak akses diperbarui untuk role {$role->name} di tenant {$tenant->name}");
                }
            }
        }

        Log::info('RiskManagementModuleSeeder berhasil dijalankan');
    }
}
