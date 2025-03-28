<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            return;
        }

        $superAdminRole = Role::where('slug', 'superadmin')->first();
        if (!$superAdminRole) {
            $this->command->info('Role superadmin tidak ditemukan.');
            return;
        }

        // Buat super admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@siar.com'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $superAdminRole->id,
                'name' => 'Super Admin',
                'password' => Hash::make('asdfasdf'),
                'is_active' => true,
            ]
        );

        $this->command->info('User super admin berhasil dibuat.');

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

        // Buat user staf
        $stafRole = Role::where('slug', 'staf')->first();
        if ($stafRole) {
            User::firstOrCreate(
                ['email' => 'staf@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $stafRole->id,
                    'name' => 'Staf',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User staf berhasil dibuat.');
        } else {
            $this->command->info('Role staf tidak ditemukan.');
        }

        // Buat user auditor internal
        $auditorRole = Role::where('slug', 'auditor-internal')->first();
        if ($auditorRole) {
            User::firstOrCreate(
                ['email' => 'auditor@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $auditorRole->id,
                    'name' => 'Auditor Internal',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User auditor internal berhasil dibuat.');
        } else {
            $this->command->info('Role auditor-internal tidak ditemukan.');
        }

        // Buat user reviewer
        $reviewerRole = Role::where('slug', 'reviewer')->first();
        if ($reviewerRole) {
            User::firstOrCreate(
                ['email' => 'reviewer@siar.com'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $reviewerRole->id,
                    'name' => 'Reviewer',
                    'password' => Hash::make('asdfasdf'),
                    'is_active' => true,
                ]
            );
            $this->command->info('User reviewer berhasil dibuat.');
        } else {
            $this->command->info('Role reviewer tidak ditemukan.');
        }
    }
}
