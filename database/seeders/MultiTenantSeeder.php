<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\RiskReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MultiTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Superadmin (tidak terikat tenant)
        User::factory()
            ->superadmin()
            ->create([
                'name' => 'Super Admin',
                'email' => 'superadmin@siar.test',
            ]);

        // Buat 2 tenant: RS A dan RS B
        $rsA = Tenant::factory()->create([
            'name' => 'Rumah Sakit A',
            'email' => 'info@rs-a.test',
        ]);

        $rsB = Tenant::factory()->create([
            'name' => 'Rumah Sakit B',
            'email' => 'info@rs-b.test',
        ]);

        // Buat user dengan berbagai role untuk RS A
        $this->createTenantUsers($rsA);

        // Buat user dengan berbagai role untuk RS B
        $this->createTenantUsers($rsB);

        // Buat laporan risiko untuk RS A
        $this->createRiskReports($rsA);

        // Buat laporan risiko untuk RS B
        $this->createRiskReports($rsB);
    }

    /**
     * Buat user dengan berbagai role untuk tenant tertentu
     */
    private function createTenantUsers(Tenant $tenant): void
    {
        // Admin RS
        $adminRS = User::factory()
            ->adminRS()
            ->create([
                'name' => 'Admin ' . $tenant->name,
                'email' => 'admin-rs@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                'tenant_id' => $tenant->id,
            ]);

        // Manajemen Eksekutif
        $eksekutif = User::factory()
            ->manajemenEksekutif()
            ->create([
                'name' => 'Eksekutif ' . $tenant->name,
                'email' => 'eksekutif@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                'tenant_id' => $tenant->id,
            ]);

        // Manajemen Operasional
        $operasional = User::factory()
            ->manajemenOperasional()
            ->create([
                'name' => 'Operasional ' . $tenant->name,
                'email' => 'operasional@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                'tenant_id' => $tenant->id,
            ]);

        // Manajemen Strategis
        $strategis = User::factory()
            ->manajemenStrategis()
            ->create([
                'name' => 'Strategis ' . $tenant->name,
                'email' => 'strategis@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                'tenant_id' => $tenant->id,
            ]);

        // Auditor Internal
        $auditor = User::factory()
            ->auditorInternal()
            ->create([
                'name' => 'Auditor ' . $tenant->name,
                'email' => 'auditor@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                'tenant_id' => $tenant->id,
            ]);

        // Staf (3 orang)
        for ($i = 1; $i <= 3; $i++) {
            User::factory()
                ->staf()
                ->create([
                    'name' => 'Staf ' . $i . ' ' . $tenant->name,
                    'email' => 'staf' . $i . '@' . strtolower(str_replace(' ', '-', $tenant->name)) . '.test',
                    'tenant_id' => $tenant->id,
                ]);
        }
    }

    /**
     * Buat laporan risiko untuk tenant tertentu
     */
    private function createRiskReports(Tenant $tenant): void
    {
        // Dapatkan staf dari tenant ini
        $staffUsers = User::where('tenant_id', $tenant->id)
            ->where('role', 'Staf')
            ->get();

        // Dapatkan Manajemen Operasional dan Eksekutif untuk tenant ini
        $operasionalUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'Manajemen Operasional')
            ->first();

        $eksekutifUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'Manajemen Eksekutif')
            ->first();

        // Buat 5 laporan status open
        foreach ($staffUsers as $staff) {
            RiskReport::factory()
                ->open()
                ->count(2)
                ->create([
                    'tenant_id' => $tenant->id,
                    'created_by' => $staff->id,
                ]);
        }

        // Buat 3 laporan status in_review
        RiskReport::factory()
            ->count(3)
            ->inReview()
            ->create([
                'tenant_id' => $tenant->id,
                'created_by' => $staffUsers->random()->id,
                'reviewed_by' => $operasionalUser->id,
            ]);

        // Buat 2 laporan status resolved
        RiskReport::factory()
            ->count(2)
            ->resolved()
            ->create([
                'tenant_id' => $tenant->id,
                'created_by' => $staffUsers->random()->id,
                'reviewed_by' => $operasionalUser->id,
                'approved_by' => $eksekutifUser->id,
            ]);
    }
}
