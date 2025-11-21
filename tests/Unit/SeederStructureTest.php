<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Module;
use App\Models\RoleModulePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeederStructureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_superadmin_role_exists()
    {
        $this->assertDatabaseHas('roles', [
            'name' => 'Superadmin'
        ]);
    }

    public function test_user_management_module_exists()
    {
        $this->assertDatabaseHas('modules', [
            'name' => 'User Management'
        ]);
    }

    public function test_role_module_permissions_not_empty()
    {
        $this->assertGreaterThan(0, RoleModulePermission::count());
    }
}
