<?php

namespace Tests\Unit;

use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantStructureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test struktur tenant dasar.
     */
    public function test_tenant_basic_structure(): void
    {
        // Buat tenant
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.localhost',
            'is_active' => true,
        ]);

        // Verifikasi tenant
        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Tenant',
            'domain' => 'test.localhost',
        ]);

        // Buat role
        $role = Role::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Admin',
            'slug' => 'test-admin',
            'description' => 'Administrator for testing',
            'is_active' => true,
        ]);

        // Verifikasi role
        $this->assertDatabaseHas('roles', [
            'tenant_id' => $tenant->id,
            'name' => 'Test Admin',
            'slug' => 'test-admin',
        ]);

        // Buat user
        $user = User::create([
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Verifikasi user
        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Buat modul
        $module = Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Module for testing',
            'is_active' => true,
        ]);

        // Verifikasi modul
        $this->assertDatabaseHas('modules', [
            'name' => 'Test Module',
            'slug' => 'test-module',
        ]);

        // Aktifkan modul untuk tenant
        $tenantModule = TenantModule::create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => true,
        ]);

        // Verifikasi tenant module
        $this->assertDatabaseHas('tenant_modules', [
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'is_active' => 1,
        ]);

        // Buat permission
        $permission = RoleModulePermission::create([
            'role_id' => $role->id,
            'module_id' => $module->id,
            'can_view' => true,
            'can_create' => true,
            'can_edit' => true,
            'can_delete' => false,
            'can_export' => true,
            'can_import' => false,
        ]);

        // Verifikasi permission
        $this->assertDatabaseHas('role_module_permissions', [
            'role_id' => $role->id,
            'module_id' => $module->id,
            'can_view' => 1,
            'can_create' => 1,
            'can_edit' => 1,
            'can_delete' => 0,
        ]);

        // Verifikasi relasi
        $this->assertEquals($tenant->id, $user->tenant_id);
        $this->assertEquals($role->id, $user->role_id);
        $this->assertEquals($tenant->id, $role->tenant_id);
        $this->assertEquals($tenant->id, $tenantModule->tenant_id);
        $this->assertEquals($module->id, $tenantModule->module_id);
        $this->assertEquals($role->id, $permission->role_id);
        $this->assertEquals($module->id, $permission->module_id);

        // Verifikasi relasi eloquent
        $this->assertEquals($tenant->id, $user->tenant->id);
        $this->assertEquals($role->id, $user->role->id);
        $this->assertEquals($tenant->id, $role->tenant->id);
        $this->assertTrue($tenant->modules->contains($module));
        $this->assertTrue($tenant->roles->contains($role));
        $this->assertTrue($tenant->users->contains($user));
        $this->assertTrue($module->tenants->contains($tenant));
    }

    /**
     * Test global scope tenant.
     */
    public function test_tenant_global_scope(): void
    {
        // Buat dua tenant
        $tenant1 = Tenant::create([
            'name' => 'Tenant One',
            'domain' => 'one.localhost',
            'is_active' => true,
        ]);

        $tenant2 = Tenant::create([
            'name' => 'Tenant Two',
            'domain' => 'two.localhost',
            'is_active' => true,
        ]);

        // Buat user untuk masing-masing tenant
        $role1 = Role::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'is_active' => true,
        ]);

        $role2 = Role::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'is_active' => true,
        ]);

        $user1 = User::create([
            'tenant_id' => $tenant1->id,
            'role_id' => $role1->id,
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::create([
            'tenant_id' => $tenant2->id,
            'role_id' => $role2->id,
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        // Tes tanpa scope tenant (karena kita dalam test dan tidak ada auth user)
        $this->assertEquals(2, User::count());
        $this->assertEquals(2, Role::count());

        // Tes dengan scope tenant manual (menggunakan method tenant())
        $this->assertEquals(1, User::where('tenant_id', $tenant1->id)->count());
        $this->assertEquals(1, Role::where('tenant_id', $tenant1->id)->count());
        $this->assertEquals(1, User::where('tenant_id', $tenant2->id)->count());
        $this->assertEquals(1, Role::where('tenant_id', $tenant2->id)->count());

        // Tes nama dan email unik dalam tenant
        $dupUser1 = User::create([
            'tenant_id' => $tenant1->id,
            'role_id' => $role1->id,
            'name' => 'Duplicate Name',
            'email' => 'unique1@example.com',
            'password' => bcrypt('password'),
        ]);

        $dupUser2 = User::create([
            'tenant_id' => $tenant2->id,
            'role_id' => $role2->id,
            'name' => 'Duplicate Name', // Nama sama di tenant berbeda
            'email' => 'unique2@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertDatabaseHas('users', ['name' => 'Duplicate Name', 'tenant_id' => $tenant1->id]);
        $this->assertDatabaseHas('users', ['name' => 'Duplicate Name', 'tenant_id' => $tenant2->id]);
    }
}
