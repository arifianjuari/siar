<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Seeder inti modul dan relasi akses
        $this->call([
            ModuleSeeder::class,
            UserManagementModuleSeeder::class,
            SPOManagementModuleSeeder::class,
            RiskManagementModuleSeeder::class,
            WorkUnitModuleSeeder::class,
            CorrespondenceManagementModuleSeeder::class,
            DocumentManagementModuleSeeder::class,
            PerformanceManagementModuleSeeder::class,
            ProductManagementModuleSeeder::class,
            ModuleProductSeeder::class,
            TenantModuleSeeder::class,
            RoleModulePermissionSeeder::class,
            KendaliMutuBiayaSeeder::class,
            KendaliMutuBiayaModuleSeeder::class,
        ]);
    }
}
