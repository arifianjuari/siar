<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan tenant RS Bhayangkara
        $tenant = Tenant::where('name', 'RS Bhayangkara Tk.III Hasta Brata Batu')->first();

        if (!$tenant) {
            $tenant = Tenant::first(); // Fallback ke tenant pertama jika RS Bhayangkara tidak ditemukan
            if (!$tenant) {
                $this->command->error('Tidak ada tenant yang tersedia.');
                return;
            }
        }

        $this->command->info("Menggunakan tenant: {$tenant->name}");

        // Cari role superadmin
        $superAdminRole = Role::where('slug', 'superadmin')->first();

        // Jika role superadmin tidak ada, buat baru
        if (!$superAdminRole) {
            $this->command->info('Membuat role superadmin...');

            // Buat role superadmin jika tidak ada
            $superAdminRole = Role::create([
                'tenant_id' => 1, // System tenant
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Super Administrator dengan akses penuh ke sistem',
                'is_active' => true,
            ]);

            $this->command->info('Role superadmin berhasil dibuat.');
        }

        // Buat user superadmin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@siar.com'],
            [
                'tenant_id' => 1, // System tenant
                'role_id' => $superAdminRole->id,
                'name' => 'Superadmin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $this->command->info('User superadmin berhasil dibuat/diperbarui.');

        // Buat admin
        $adminRole = Role::where('slug', 'tenant-admin')->first();
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $adminRole->id,
                    'name' => 'Tenant Admin',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User tenant admin berhasil dibuat.');
        } else {
            $this->command->info('Role tenant-admin tidak ditemukan.');
        }

        // Buat user manajemen strategis
        $strategisRole = Role::where('slug', 'manajemen-strategis')->first();
        if ($strategisRole) {
            User::firstOrCreate(
                ['email' => 'strategis@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $strategisRole->id,
                    'name' => 'Manajemen Strategis',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User manajemen strategis berhasil dibuat.');
        } else {
            $this->command->info('Role manajemen-strategis tidak ditemukan.');
        }

        // Buat user manajemen eksekutif
        $eksekutifRole = Role::where('slug', 'manajemen-eksekutif')->first();
        if ($eksekutifRole) {
            User::firstOrCreate(
                ['email' => 'eksekutif@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $eksekutifRole->id,
                    'name' => 'Manajemen Eksekutif',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User manajemen eksekutif berhasil dibuat.');
        } else {
            $this->command->info('Role manajemen-eksekutif tidak ditemukan.');
        }

        // Buat user manajemen operasional
        $operasionalRole = Role::where('slug', 'manajemen-operasional')->first();
        if ($operasionalRole) {
            User::firstOrCreate(
                ['email' => 'operasional@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $operasionalRole->id,
                    'name' => 'Manajemen Operasional',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User manajemen operasional berhasil dibuat.');
        } else {
            $this->command->info('Role manajemen-operasional tidak ditemukan.');
        }

        // Ambil unit kerja yang tersedia untuk tenant
        $workUnit = WorkUnit::where('tenant_id', $tenant->id)->first();

        if ($workUnit) {
            $this->command->info("Menggunakan unit kerja: {$workUnit->unit_name}");
        } else {
            $this->command->warn("Tidak ada unit kerja yang tersedia untuk tenant {$tenant->name}");
        }

        // Buat user staf dan kaitkan dengan unit kerja
        $stafRole = Role::where('slug', 'staf')->first();
        if ($stafRole) {
            $staf = User::firstOrCreate(
                ['email' => 'staf@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $stafRole->id,
                    'name' => 'Staf',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );

            // Kaitkan user dengan unit kerja
            if ($workUnit) {
                $staf->work_unit_id = $workUnit->id;
                $staf->save();
                $this->command->info('User staf berhasil dikaitkan dengan unit kerja: ' . $workUnit->unit_name);
            } else {
                $this->command->warn('Tidak ada unit kerja yang tersedia untuk dikaitkan dengan user staf.');
            }

            $this->command->info('User staf berhasil dibuat.');
        } else {
            $this->command->info('Role staf tidak ditemukan.');
        }

        // Buat user auditor internal
        $auditorRole = Role::where('slug', 'auditor-internal')->first();
        if ($auditorRole) {
            $auditor = User::firstOrCreate(
                ['email' => 'auditor@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $auditorRole->id,
                    'name' => 'Auditor Internal',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );

            // Kaitkan user dengan unit kerja
            if ($workUnit) {
                $auditor->work_unit_id = $workUnit->id;
                $auditor->save();
                $this->command->info('User auditor berhasil dikaitkan dengan unit kerja: ' . $workUnit->unit_name);
            }

            $this->command->info('User auditor internal berhasil dibuat.');
        } else {
            $this->command->info('Role auditor-internal tidak ditemukan.');
        }

        // Buat user reviewer
        $reviewerRole = Role::where('slug', 'reviewer')->first();
        if ($reviewerRole) {
            $reviewer = User::firstOrCreate(
                ['email' => 'reviewer@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $reviewerRole->id,
                    'name' => 'Reviewer',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );

            // Kaitkan user dengan unit kerja
            if ($workUnit) {
                $reviewer->work_unit_id = $workUnit->id;
                $reviewer->save();
                $this->command->info('User reviewer berhasil dikaitkan dengan unit kerja: ' . $workUnit->unit_name);
            }

            $this->command->info('User reviewer berhasil dibuat.');
        } else {
            $this->command->info('Role reviewer tidak ditemukan.');
        }
    }
}
