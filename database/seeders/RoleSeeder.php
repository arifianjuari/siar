<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
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

        $roles = [
            [
                'name' => 'Admin RS',
                'description' => 'Administrator Rumah Sakit',
            ],
            [
                'name' => 'Manajemen Strategis',
                'description' => 'Manajemen level strategis',
            ],
            [
                'name' => 'Manajemen Eksekutif',
                'description' => 'Manajemen level eksekutif',
            ],
            [
                'name' => 'Manajemen Operasional',
                'description' => 'Manajemen level operasional',
            ],
            [
                'name' => 'Staf',
                'description' => 'Staf pelaksana',
            ],
            [
                'name' => 'Auditor Internal',
                'description' => 'Auditor Internal RS',
            ],
            [
                'name' => 'Reviewer',
                'description' => 'Reviewer dokumen',
            ],
        ];

        foreach ($roles as $role) {
            $slug = Str::slug($role['name']);
            Role::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'slug' => $slug
                ],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Role berhasil dibuat atau diperbarui.');
    }
}
