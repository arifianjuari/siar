<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class SystemTenantAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan tenant System
        $systemTenant = Tenant::where('name', 'System')->first();

        if (!$systemTenant) {
            $this->command->error('Tenant System tidak ditemukan!');
            return;
        }

        // Mendapatkan role tenant-admin
        $tenantAdminRole = Role::where('slug', 'tenant-admin')->first();

        if (!$tenantAdminRole) {
            $this->command->error('Role tenant-admin tidak ditemukan!');
            return;
        }

        // Data admin tenant System
        $adminData = [
            [
                'name' => 'Admin System',
                'email' => 'admin.system@siar.test',
                'password' => Hash::make('password'),
                'tenant_id' => $systemTenant->id,
                'role_id' => $tenantAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manager System',
                'email' => 'manager.system@siar.test',
                'password' => Hash::make('password'),
                'tenant_id' => $systemTenant->id,
                'role_id' => $tenantAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        ];

        // Membuat admin tenant
        foreach ($adminData as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }

        $this->command->info('Admin tenant System berhasil dibuat!');
    }
}
