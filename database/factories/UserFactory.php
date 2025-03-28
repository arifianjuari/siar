<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'remember_token' => Str::random(10),
            'role_id' => Role::inRandomOrder()->first()->id, // Gunakan role_id yang sudah ada
            'tenant_id' => Tenant::factory(),
            'is_active' => true,
        ];
    }

    /**
     * State for a specific role by role slug.
     */
    public function withRole(string $roleSlug): Factory
    {
        return $this->state(function (array $attributes) use ($roleSlug) {
            $role = Role::where('slug', $roleSlug)->first();

            return [
                'role_id' => $role ? $role->id : null,
            ];
        });
    }

    /**
     * State for superadmin role.
     */
    public function superadmin(): Factory
    {
        return $this->withRole('superadmin');
    }

    /**
     * State for admin role.
     */
    public function adminRS(): Factory
    {
        return $this->withRole('tenant-admin');
    }

    /**
     * State for manajemen operasional.
     */
    public function manajemenOperasional(): Factory
    {
        return $this->withRole('manajemen-operasional');
    }

    /**
     * State for manajemen eksekutif.
     */
    public function manajemenEksekutif(): Factory
    {
        return $this->withRole('manajemen-eksekutif');
    }

    /**
     * State for manajemen strategis.
     */
    public function manajemenStrategis(): Factory
    {
        return $this->withRole('manajemen-strategis');
    }

    /**
     * State for auditor internal.
     */
    public function auditorInternal(): Factory
    {
        return $this->withRole('auditor-internal');
    }

    /**
     * State for staf.
     */
    public function staf(): Factory
    {
        return $this->withRole('staf');
    }
}
