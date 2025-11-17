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
        // Buat tenant
        $tenant = Tenant::create([
            'name' => 'RS Bhayangkara Tk.III Hasta Brata Batu',
            'domain' => 'rsbbatu',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        // Buat user admin
        $adminUser = User::create([
            'name' => 'Admin RSBB',
            'email' => 'adminrsbbatu@gmail.com',
            'password' => Hash::make('asdfasdf'),
            'tenant_id' => $tenant->id,
            'role_id' => $adminRole->id,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Tenant RS Bhayangkara Tk.III Hasta Brata Batu berhasil dibuat!');
        $this->command->info('Email: adminrsbbatu@gmail.com');
        $this->command->info('Password: asdfasdf');
    }
}
