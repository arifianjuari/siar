<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    protected PermissionService $permissionService;

    /**
     * Module code for permission checking
     * Must be overridden in child classes
     */
    protected string $moduleCode = '';

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Perform pre-authorization checks (superadmin bypass)
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability)
    {
        // Superadmin bypasses all checks
        if ($user->isSuperadmin()) {
            return true;
        }

        // Check if user has access to the module first
        if (!$this->permissionService->userHasModuleAccess($user, $this->getModuleCode())) {
            return false;
        }

        return null;
    }

    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_view');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $model)) {
            return false;
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_view');
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_create');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $model)) {
            return false;
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_edit');
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $model)) {
            return false;
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_delete');
    }

    /**
     * Determine if the user can restore the model.
     */
    public function restore(User $user, Model $model): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $model)) {
            return false;
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_edit');
    }

    /**
     * Determine if the user can permanently delete the model.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $model)) {
            return false;
        }

        // Usually requires special permission or superadmin
        return false;
    }

    /**
     * Determine if the user can export models.
     */
    public function export(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_export');
    }

    /**
     * Determine if the user can import models.
     */
    public function import(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_import');
    }

    /**
     * Check if user has access to the model based on tenant
     */
    protected function checkTenantAccess(User $user, Model $model): bool
    {
        // Skip check for superadmin
        if ($user->isSuperadmin()) {
            return true;
        }

        // Check if model has tenant_id property
        if (!property_exists($model, 'tenant_id') && !isset($model->tenant_id)) {
            return true; // Model is not tenant-scoped
        }

        // Check if user belongs to the same tenant as the model
        return $user->tenant_id === $model->tenant_id;
    }

    /**
     * Get the module code for permission checking
     * Can be overridden in child classes if needed
     */
    protected function getModuleCode(): string
    {
        if (empty($this->moduleCode)) {
            throw new \Exception('Module code must be set in policy class: ' . get_class($this));
        }
        
        return $this->moduleCode;
    }
}
