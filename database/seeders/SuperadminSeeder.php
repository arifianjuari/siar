<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat tenant system jika belum ada
        $tenant = Tenant::firstOrCreate(
            ['name' => 'System'],
            [
                'domain' => 'system',
                'database' => 'system',
                'is_active' => true,
            ]
        );

        // 2. Buat role superadmin jika belum ada
        $role = Role::firstOrCreate(
            ['slug' => 'superadmin', 'tenant_id' => $tenant->id],
            [
                'name' => 'Superadmin',
                'description' => 'Administrator Sistem dengan akses penuh',
                'is_active' => true,
            ]
        );

        // 3. Buat user superadmin
        $user = User::firstOrCreate(
            ['email' => 'superadmin@siar.com'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $role->id,
                'name' => 'Superadmin',
                'password' => Hash::make('asdfasdf'),
                'is_active' => true,
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->command->info('User superadmin berhasil dibuat!');
        } else {
            // Update password jika user sudah ada
            $user->update([
                'password' => Hash::make('asdfasdf'),
                'is_active' => true,
            ]);
            $this->command->info('User superadmin sudah ada, password diperbarui.');
        }
    }
}
