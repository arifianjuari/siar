<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy extends BasePolicy
{
    /**
     * Module code for role management
     */
    protected string $moduleCode = 'user-management';

    /**
     * Override to add additional check for superadmin roles
     * Superadmin roles should only be manageable by superadmins
     */
    public function update(User $user, $model): bool
    {
        // Check base permission
        if (!parent::update($user, $model)) {
            return false;
        }

        // If modifying a superadmin role, only superadmin can do it
        if (isset($model->name) && strtolower($model->name) === 'super admin') {
            return $user->isSuperadmin();
        }

        return true;
    }

    /**
     * Override to prevent deletion of superadmin and system roles
     */
    public function delete(User $user, $model): bool
    {
        // Check base permission
        if (!parent::delete($user, $model)) {
            return false;
        }

        // Cannot delete superadmin role
        if (isset($model->name) && strtolower($model->name) === 'super admin') {
            return false;
        }

        // Cannot delete system roles
        if (isset($model->is_system) && $model->is_system) {
            return false;
        }

        return true;
    }
}
