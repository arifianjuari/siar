<?php

namespace Modules\SPOManagement\Policies;

use App\Models\User;
use App\Policies\BasePolicy;
use Illuminate\Support\Facades\Log;

class SPOPolicy extends BasePolicy
{

    /**
     * Module code for SPO management
     */
    protected string $moduleCode = 'spo-management';

    /**
     * Determine if user can view any SPO
     */
    public function viewAny(User $user): bool
    {
        $canView = $this->permissionService->userHasPermission($user, $this->moduleCode, 'can_view');
        Log::debug('SPOPolicy::viewAny', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'result' => $canView
        ]);
        return $canView;
    }

    /**
     * Determine if user can view specific SPO
     * Ensures tenant isolation
     */
    public function view(User $user, $model): bool
    {
        // Check permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_view')) {
            return false;
        }

        // Ensure tenant isolation
        $canView = !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
        
        Log::debug('SPOPolicy::view', [
            'user_id' => $user->id,
            'spo_id' => $model->id ?? null,
            'tenant_match' => $canView
        ]);
        
        return $canView;
    }

    /**
     * Determine if user can create SPO
     */
    public function create(User $user): bool
    {
        $canCreate = $this->permissionService->userHasPermission($user, $this->moduleCode, 'can_create');
        Log::debug('SPOPolicy::create', [
            'user_id' => $user->id,
            'result' => $canCreate
        ]);
        return $canCreate;
    }

    /**
     * Determine if user can update SPO
     * Ensures tenant isolation
     */
    public function update(User $user, $model): bool
    {
        // Check permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_edit')) {
            return false;
        }

        // Ensure tenant isolation
        $canUpdate = !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
        
        Log::debug('SPOPolicy::update', [
            'user_id' => $user->id,
            'spo_id' => $model->id ?? null,
            'tenant_match' => $canUpdate
        ]);
        
        return $canUpdate;
    }

    /**
     * Determine if user can delete SPO
     * Ensures tenant isolation
     */
    public function delete(User $user, $model): bool
    {
        // Check permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_delete')) {
            return false;
        }

        // Ensure tenant isolation
        $canDelete = !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
        
        Log::debug('SPOPolicy::delete', [
            'user_id' => $user->id,
            'spo_id' => $model->id ?? null,
            'tenant_match' => $canDelete
        ]);
        
        return $canDelete;
    }

    /**
     * Determine if user can restore deleted SPO
     */
    public function restore(User $user, $model): bool
    {
        // Check edit permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_edit')) {
            return false;
        }

        // Ensure tenant isolation
        return !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine if user can force delete SPO
     * Only superadmin can permanently delete
     */
    public function forceDelete(User $user, $model): bool
    {
        // Only superadmin can force delete
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Ensure tenant isolation
        return !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
    }
}
