<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixRolePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:fix-permissions {tenant_id? : ID tenant yang akan diperbaiki}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbaiki izin untuk role tenant-admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        if ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
            if ($tenants->isEmpty()) {
                $this->error("Tenant dengan ID {$tenantId} tidak ditemukan!");
                return 1;
            }
        } else {
            $tenants = Tenant::all();
        }

        foreach ($tenants as $tenant) {
            $this->info("Memperbaiki izin untuk tenant: {$tenant->name} (ID: {$tenant->id})");

            // Temukan role tenant_admin
            $adminRole = Role::where('tenant_id', $tenant->id)
                ->where(function ($query) {
                    $query->where('slug', 'tenant-admin')
                        ->orWhere('slug', 'tenant-admin');
                })->first();

            if (!$adminRole) {
                $this->warn("  - Role tenant-admin tidak ditemukan untuk tenant ini. Melewati...");
                continue;
            }

            $this->info("  - Memperbaiki izin untuk role: {$adminRole->name} (ID: {$adminRole->id})");

            // Dapatkan semua modul yang diaktifkan untuk tenant
            $modules = Module::whereIn('id', function ($query) use ($tenant) {
                $query->select('module_id')
                    ->from('tenant_modules')
                    ->where('tenant_id', $tenant->id)
                    ->where('is_active', true);
            })->get();

            $this->info("  - Modul aktif ditemukan: " . $modules->count());

            foreach ($modules as $module) {
                $this->info("    - Menambahkan izin untuk modul: {$module->name}");

                // Periksa apakah izin sudah ada
                $permission = RoleModulePermission::where('role_id', $adminRole->id)
                    ->where('module_id', $module->id)
                    ->first();

                if (!$permission) {
                    // Tambahkan izin baru
                    RoleModulePermission::create([
                        'role_id' => $adminRole->id,
                        'module_id' => $module->id,
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true,
                    ]);
                    $this->info("      + Izin ditambahkan untuk modul: {$module->name}");
                } else {
                    // Update izin yang sudah ada untuk memastikan semua izin diaktifkan
                    $permission->update([
                        'can_view' => true,
                        'can_create' => true,
                        'can_edit' => true,
                        'can_delete' => true,
                        'can_export' => true,
                        'can_import' => true,
                    ]);
                    $this->line("      - Izin diperbarui untuk modul: {$module->name}");
                }
            }

            // Pastikan semua izin khusus untuk risk-analysis ada
            $this->ensureRiskAnalysisPermissions($tenant, $adminRole);

            $this->info("  - Selesai memperbaiki izin untuk {$adminRole->name}");
            $this->newLine();
        }

        $this->info("Perbaikan izin selesai!");
        return 0;
    }

    /**
     * Memastikan izin khusus untuk risk-analysis ada
     */
    private function ensureRiskAnalysisPermissions($tenant, $adminRole)
    {
        $this->info("    - Memastikan izin khusus untuk risk-analysis");

        // Cek apakah modul risk-management aktif
        $moduleActive = DB::table('tenant_modules')
            ->where('tenant_id', $tenant->id)
            ->whereIn('module_id', function ($query) {
                $query->select('id')
                    ->from('modules')
                    ->where('slug', 'risk-management');
            })
            ->where('is_active', true)
            ->exists();

        if (!$moduleActive) {
            $this->warn("      - Modul risk-management tidak aktif untuk tenant ini. Melewati...");
            return;
        }

        // Cek atau buat konfigurasi modul untuk fitur analisis risiko
        $configExists = DB::table('tenant_module_configs')
            ->where('tenant_id', $tenant->id)
            ->where('module', 'risk_management')
            ->where('feature', 'risk_analysis')
            ->exists();

        if (!$configExists) {
            $this->info("      + Membuat konfigurasi modul untuk fitur analisis risiko");
            DB::table('tenant_module_configs')->insert([
                'tenant_id' => $tenant->id,
                'module' => 'risk_management',
                'feature' => 'risk_analysis',
                'allowed_roles' => json_encode([$adminRole->id]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Update konfigurasi yang ada, tambahkan role admin jika belum ada
            $config = DB::table('tenant_module_configs')
                ->where('tenant_id', $tenant->id)
                ->where('module', 'risk_management')
                ->where('feature', 'risk_analysis')
                ->first();

            $allowedRoles = json_decode($config->allowed_roles) ?? [];
            if (!in_array($adminRole->id, $allowedRoles)) {
                $allowedRoles[] = $adminRole->id;

                DB::table('tenant_module_configs')
                    ->where('id', $config->id)
                    ->update([
                        'allowed_roles' => json_encode($allowedRoles),
                        'updated_at' => now(),
                    ]);

                $this->info("      + Role admin ditambahkan ke konfigurasi analisis risiko");
            } else {
                $this->line("      - Role admin sudah ada dalam konfigurasi analisis risiko");
            }
        }
    }
}
