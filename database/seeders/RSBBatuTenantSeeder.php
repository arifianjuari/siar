<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RSBBatuTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat atau ambil tenant (idempotent)
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'rsbbatu'],
            [
                'name' => 'RS Bhayangkara Tk.III Hasta Brata Batu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Buat role tenant admin (or get existing)
        $adminRole = Role::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'slug' => 'tenant-admin',
            ],
            [
                'name' => 'Tenant Admin',
                'description' => 'Administrator RS Bhayangkara Tk.III Hasta Brata Batu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Buat atau update user admin (idempotent berdasarkan email)
        $adminUser = User::updateOrCreate(
            [
                'email' => 'adminrsbbatu@gmail.com',
            ],
            [
                'name' => 'Admin RSBB',
                'password' => Hash::make('asdfasdf'),
                'tenant_id' => $tenant->id,
                'role_id' => $adminRole->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Tenant RS Bhayangkara Tk.III Hasta Brata Batu tersedia (dibuat atau sudah ada).');
        $this->command->info('Email: adminrsbbatu@gmail.com');
        $this->command->info('Password: asdfasdf');
    }
}
