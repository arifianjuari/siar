<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MultiTenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 2 tenant
        $tenants = [
            [
                'name' => 'system',
                'description' => 'Tenant untuk superadmin melakukan testing',
            ],
            [
                'name' => 'RS Bhayangkara Tk.III Batu',
                'description' => 'Tenant utama untuk operasional RS',
            ]
        ];

        foreach ($tenants as $tenantData) {
            $tenant = Tenant::firstOrCreate(['name' => $tenantData['name']], $tenantData);

            // 2. Buat admin untuk setiap tenant
            $admin = User::updateOrCreate(
                [
                    'email' => 'admin@' . str_replace(' ', '', strtolower($tenant->name)) . '.com'
                ],
                [
                    'name' => 'Admin ' . $tenant->name,
                    'password' => Hash::make('asdfasdf'),
                    'tenant_id' => $tenant->id,
                ]
            );
            $admin->assignRole('Tenant Admin');

            // 3. Jika tenant adalah RS Bhayangkara, buat user untuk semua role
            if ($tenant->name === 'RS Bhayangkara Tk.III Batu') {
                $roles = [
                    'Tenant Admin',
                    'Manajemen Strategis',
                    'Manajemen Eksekutif',
                    'Manajemen Operasional',
                    'Staf',
                    'Auditor Internal',
                    'Reviewer',
                ];

                foreach ($roles as $roleName) {
                    // Skip admin yang sudah dibuat
                    if ($roleName === 'Tenant Admin') continue;

                    $user = User::updateOrCreate(
                        [
                            'email' => strtolower(str_replace(' ', '', $roleName)) . '@bhayangkara.com'
                        ],
                        [
                            'name' => $roleName . ' Bhayangkara',
                            'password' => Hash::make('asdfasdf'),
                            'tenant_id' => $tenant->id,
                        ]
                    );
                    $user->assignRole($roleName);
                }
            }
        }
    }
}
