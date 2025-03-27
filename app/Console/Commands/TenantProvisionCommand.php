<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantProvisionCommand extends Command
{
    /**
     * Nama dan signature dari command
     *
     * @var string
     */
    protected $signature = 'tenant:provision
                            {--name= : Nama tenant}
                            {--domain= : Domain tenant}
                            {--admin-name= : Nama admin tenant}
                            {--admin-email= : Email admin tenant}
                            {--admin-password= : Password admin tenant}';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Membuat tenant baru beserta admin dan setup awal';

    /**
     * Default modules yang akan diaktifkan
     *
     * @var array
     */
    protected $defaultModules = [
        'user-management',
        'dashboard',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('--- Memulai Provisioning Tenant Baru ---');

        // Validasi input
        $tenantName = $this->option('name') ?? $this->ask('Masukkan nama tenant');
        $tenantDomain = $this->option('domain') ?? $this->ask('Masukkan domain tenant');
        $adminName = $this->option('admin-name') ?? $this->ask('Masukkan nama admin');
        $adminEmail = $this->option('admin-email') ?? $this->ask('Masukkan email admin');
        $adminPassword = $this->option('admin-password') ?? $this->secret('Masukkan password admin') ?? 'asdfasdf';

        // Validasi domain unik
        if (Tenant::where('domain', $tenantDomain)->exists()) {
            $this->error("Domain '$tenantDomain' sudah digunakan. Silakan gunakan domain lain.");
            return 1;
        }

        // Mulai transaction DB
        DB::beginTransaction();

        try {
            // 1. Buat tenant
            $tenant = $this->createTenant($tenantName, $tenantDomain);
            $this->info("✅ Tenant berhasil dibuat: $tenantName ($tenantDomain)");

            // 2. Buat role admin
            $adminRole = $this->createAdminRole($tenant);
            $this->info("✅ Role admin berhasil dibuat untuk tenant: $tenantName");

            // 3. Buat user admin
            $admin = $this->createAdminUser($tenant, $adminRole, $adminName, $adminEmail, $adminPassword);
            $this->info("✅ User admin berhasil dibuat: $adminEmail");

            // 4. Aktifkan modul default
            $this->activateDefaultModules($tenant, $adminRole);
            $this->info("✅ Modul default berhasil diaktifkan");

            // 5. Jalankan seeder tambahan jika diperlukan
            $this->runAdditionalSeeders($tenant);
            $this->info("✅ Data awal berhasil dibuat");

            // Commit transaction
            DB::commit();

            $this->newLine();
            $this->info('=== Tenant Berhasil Dibuat ===');
            $this->table(
                ['Tenant', 'Domain', 'Admin', 'Email'],
                [[$tenantName, $tenantDomain, $adminName, $adminEmail]]
            );
            $this->info('Silakan login menggunakan kredensial admin yang telah dibuat.');

            return 0;
        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            $this->error("Tenant gagal dibuat.");
            return 1;
        }
    }

    /**
     * Buat tenant baru
     */
    private function createTenant(string $name, string $domain): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'database' => 'db_' . $domain,
            'is_active' => true,
        ]);
    }

    /**
     * Buat role admin untuk tenant
     */
    private function createAdminRole(Tenant $tenant): Role
    {
        return Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tenant Admin',
            'slug' => 'tenant-admin',
            'description' => 'Administrator untuk tenant ' . $tenant->name,
            'is_active' => true,
        ]);
    }

    /**
     * Buat user admin untuk tenant
     */
    private function createAdminUser(Tenant $tenant, Role $role, string $name, string $email, string $password): User
    {
        return User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }

    /**
     * Aktifkan modul default untuk tenant
     */
    private function activateDefaultModules(Tenant $tenant, Role $adminRole): void
    {
        // Cek apakah modul sudah ada, jika belum, buat modul default
        $this->createDefaultModulesIfNotExists();

        // Aktifkan modul untuk tenant
        foreach ($this->defaultModules as $moduleSlug) {
            $module = Module::where('code', $moduleSlug)->first();

            if ($module) {
                // Aktifkan modul untuk tenant
                TenantModule::create([
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'is_active' => true,
                ]);

                // Berikan semua izin untuk admin
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
            }
        }
    }

    /**
     * Buat modul default jika belum ada
     */
    private function createDefaultModulesIfNotExists(): void
    {
        $defaultModulesData = [
            [
                'name' => 'User Management',
                'code' => 'user-management',
                'description' => 'Manajemen pengguna dan hak akses',
                'is_active' => true,
            ],
            [
                'name' => 'Dashboard',
                'code' => 'dashboard',
                'description' => 'Dashboard aplikasi',
                'is_active' => true,
            ],
        ];

        foreach ($defaultModulesData as $moduleData) {
            Module::firstOrCreate(
                ['code' => $moduleData['code']],
                $moduleData
            );
        }
    }

    /**
     * Jalankan seeder tambahan jika diperlukan
     */
    private function runAdditionalSeeders(Tenant $tenant): void
    {
        // Di sini Anda bisa menambahkan kode untuk menjalankan seeder tambahan
        // Contoh: Membuat data dummy, mengisi master data, dll.
        // 
        // Catatan: Karena kita tidak mengimplementasikan seeder terpisah,
        // fungsi ini hanya placeholder untuk implementasi tambahan.
    }
}
