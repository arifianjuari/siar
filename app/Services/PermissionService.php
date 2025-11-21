<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use App\Models\RoleModulePermission;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Cache duration in minutes (60 minutes = 1 hour)
     */
    const CACHE_DURATION = 60;

    /**
     * Check if user has specific permission for a module
     *
     * @param User $user
     * @param string $moduleCode
     * @param string $permission
     * @return bool
     */
    public function userHasPermission(User $user, string $moduleCode, string $permission): bool
    {
        // Superadmin always has all permissions
        if ($user->isSuperadmin()) {
            return true;
        }

        // User must belong to the same tenant
        if (!$user->tenant_id) {
            Log::warning('User without tenant_id attempting to access permission', [
                'user_id' => $user->id,
                'module_code' => $moduleCode,
                'permission' => $permission
            ]);
            return false;
        }

        // Normalize potential legacy/alias module codes
        $moduleCode = $this->normalizeModuleCode($moduleCode);

        // Use cached permissions if available
        $cacheKey = $this->getCacheKey($user, $moduleCode);
        $permissions = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user, $moduleCode) {
            return $this->fetchUserPermissions($user, $moduleCode);
        });

        return $permissions[$permission] ?? false;
    }

    /**
     * Check if user has module access
     *
     * @param User $user
     * @param string $moduleCode
     * @return bool
     */
    public function userHasModuleAccess(User $user, string $moduleCode): bool
    {
        // Superadmin always has access
        if ($user->isSuperadmin()) {
            return true;
        }

        // Normalize potential legacy/alias module codes
        $moduleCode = $this->normalizeModuleCode($moduleCode);

        // Get module by code or slug
        $module = Module::where('code', $moduleCode)
            ->orWhere('slug', $moduleCode)
            ->first();
        
        if (!$module) {
            Log::warning('PermissionService: module not found', [
                'module_code' => $moduleCode,
            ]);
            return false;
        }

        // Check if tenant has module activated (use pivot by module_id to avoid code/slug mismatch)
        if (!$user->tenant) {
            Log::warning('PermissionService: user has no tenant when checking module access', [
                'user_id' => $user->id,
                'module_id' => $module->id,
                'module_code' => $moduleCode,
            ]);
            return false;
        }

        $isModuleActiveForTenant = $user->tenant->modules()
            ->where('modules.id', $module->id)
            ->wherePivot('is_active', true)
            ->exists();

        if (!$isModuleActiveForTenant) {
            Log::info('PermissionService: module inactive for tenant', [
                'tenant_id' => $user->tenant_id,
                'module_id' => $module->id,
                'module_code' => $moduleCode,
            ]);
            return false;
        }

        $hasAnyPermission = RoleModulePermission::where('role_id', $user->role_id)
            ->where('module_id', $module->id)
            ->where(function($query) {
                $query->where('can_view', true)
                    ->orWhere('can_create', true)
                    ->orWhere('can_edit', true)
                    ->orWhere('can_delete', true)
                    ->orWhere('can_import', true)
                    ->orWhere('can_export', true);
            })
            ->exists();

        if (!$hasAnyPermission) {
            Log::info('PermissionService: role has no permissions for module', [
                'role_id' => $user->role_id,
                'module_id' => $module->id,
                'module_code' => $moduleCode,
            ]);
        }

        return $hasAnyPermission;
    }

    /**
     * Get all permissions for a user for a specific module
     *
     * @param User $user
     * @param string $moduleCode
     * @return array
     */
    public function getUserModulePermissions(User $user, string $moduleCode): array
    {
        // Superadmin has all permissions
        if ($user->isSuperadmin()) {
            return [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
                'can_import' => true,
                'can_export' => true,
            ];
        }

        $cacheKey = $this->getCacheKey($user, $moduleCode);
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user, $moduleCode) {
            return $this->fetchUserPermissions($user, $moduleCode);
        });
    }

    /**
     * Fetch user permissions from database with hierarchy and overrides
     *
     * @param User $user
     * @param string $moduleCode
     * @return array
     */
    private function fetchUserPermissions(User $user, string $moduleCode): array
    {
        // Default permissions
        $permissions = [
            'can_view' => false,
            'can_create' => false,
            'can_edit' => false,
            'can_delete' => false,
            'can_import' => false,
            'can_export' => false,
        ];

        // Get module by code or slug
        $module = Module::where('code', $moduleCode)
            ->orWhere('slug', $moduleCode)
            ->first();
        
        if (!$module) {
            return $permissions;
        }

        // Get role permissions with hierarchy
        $rolePermissions = $this->getRolePermissionsWithHierarchy($user->role_id, $module->id);
        if ($rolePermissions) {
            $permissions = [
                'can_view' => (bool) $rolePermissions->can_view,
                'can_create' => (bool) $rolePermissions->can_create,
                'can_edit' => (bool) $rolePermissions->can_edit,
                'can_delete' => (bool) $rolePermissions->can_delete,
                'can_import' => (bool) $rolePermissions->can_import,
                'can_export' => (bool) $rolePermissions->can_export,
            ];
        }

        // Apply user-level overrides
        $userOverrides = $this->getUserPermissionOverrides($user->id, $module->id);
        if ($userOverrides) {
            $permissions = $this->applyUserOverrides($permissions, $userOverrides);
        }

        return $permissions;
    }

    /**
     * Clear cache for a specific user
     *
     * @param User $user
     * @param string|null $moduleCode
     */
    public function clearUserCache(User $user, ?string $moduleCode = null): void
    {
        if ($moduleCode) {
            // Clear specific module cache for user
            Cache::forget($this->getCacheKey($user, $moduleCode));
        } else {
            // Clear all module caches for this user
            $modules = Module::all();
            foreach ($modules as $module) {
                Cache::forget($this->getCacheKey($user, $module->code));
            }
        }
    }

    /**
     * Clear all permission caches for a tenant
     *
     * @param int $tenantId
     */
    public function clearTenantCache(int $tenantId): void
    {
        // Clear all caches for users in this tenant
        // Note: This is a simplified version for file cache driver
        // For production, consider using Redis or Memcached which support tags
        $users = User::where('tenant_id', $tenantId)->get();
        foreach ($users as $user) {
            $this->clearUserCache($user);
        }
    }

    /**
     * Clear all permission caches
     */
    public function clearAllCaches(): void
    {
        // Clear all permission-related caches by pattern
        // Note: This is a simplified version for file cache driver
        // For production, consider using Redis or Memcached which support tags
        Cache::flush();
    }

    /**
     * Get cache key for user permissions
     *
     * @param User $user
     * @param string $moduleCode
     * @return string
     */
    private function getCacheKey(User $user, string $moduleCode): string
    {
        return sprintf(
            'permissions:tenant_%d:user_%d:module_%s',
            $user->tenant_id,
            $user->id,
            $moduleCode
        );
    }

    /**
     * Normalize known legacy or alias module codes to current slugs
     */
    private function normalizeModuleCode(string $moduleCode): string
    {
        $map = [
            'correspondence-management' => 'correspondence',
            'work-units' => 'work-unit',
        ];
        return $map[$moduleCode] ?? $moduleCode;
    }

    /**
     * Check multiple permissions at once
     *
     * @param User $user
     * @param string $moduleCode
     * @param array $permissions
     * @return bool
     */
    public function userHasAnyPermission(User $user, string $moduleCode, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->userHasPermission($user, $moduleCode, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all specified permissions
     *
     * @param User $user
     * @param string $moduleCode
     * @param array $permissions
     * @return bool
     */
    public function userHasAllPermissions(User $user, string $moduleCode, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->userHasPermission($user, $moduleCode, $permission)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Get role permissions with hierarchy support
     *
     * @param int $roleId
     * @param int $moduleId
     * @return mixed
     */
    private function getRolePermissionsWithHierarchy(int $roleId, int $moduleId)
    {
        // Get the role with its hierarchy chain
        $role = Role::find($roleId);
        if (!$role) {
            return null;
        }

        // Get direct permissions for this role
        $directPermissions = RoleModulePermission::where('role_id', $roleId)
            ->where('module_id', $moduleId)
            ->first();

        // If role has direct permissions or doesn't inherit, return them
        if ($directPermissions || !$role->inherit_permissions) {
            return $directPermissions;
        }

        // Check parent role if inheritance is enabled
        if ($role->parent_role_id) {
            return $this->getRolePermissionsWithHierarchy($role->parent_role_id, $moduleId);
        }

        return null;
    }

    /**
     * Get user permission overrides
     *
     * @param int $userId
     * @param int $moduleId
     * @return mixed
     */
    private function getUserPermissionOverrides(int $userId, int $moduleId)
    {
        return DB::table('user_permissions')
            ->where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    /**
     * Apply user overrides to base permissions
     *
     * @param array $permissions
     * @param mixed $overrides
     * @return array
     */
    private function applyUserOverrides(array $permissions, $overrides): array
    {
        $permissionKeys = ['can_view', 'can_create', 'can_edit', 'can_delete', 'can_import', 'can_export'];
        
        foreach ($permissionKeys as $key) {
            if (isset($overrides->$key)) {
                if ($overrides->type === 'grant') {
                    // Grant adds the permission
                    $permissions[$key] = $permissions[$key] || (bool) $overrides->$key;
                } else {
                    // Revoke removes the permission
                    $permissions[$key] = $permissions[$key] && !(bool) $overrides->$key;
                }
            }
        }

        return $permissions;
    }

    /**
     * Get all roles in hierarchy chain
     *
     * @param int $roleId
     * @return array
     */
    public function getRoleHierarchyChain(int $roleId): array
    {
        $chain = [];
        $currentRoleId = $roleId;
        $maxDepth = 10; // Prevent infinite loops
        $depth = 0;

        while ($currentRoleId && $depth < $maxDepth) {
            $role = Role::find($currentRoleId);
            if (!$role) {
                break;
            }

            $chain[] = $role;
            
            if (!$role->inherit_permissions) {
                break;
            }

            $currentRoleId = $role->parent_role_id;
            $depth++;
        }

        return $chain;
    }

    /**
     * Grant temporary permission override to user
     *
     * @param User $user
     * @param string $moduleCode
     * @param array $permissions
     * @param string $reason
     * @param \DateTime|null $expiresAt
     * @return bool
     */
    public function grantUserPermissionOverride(
        User $user, 
        string $moduleCode, 
        array $permissions, 
        string $reason, 
        \DateTime $expiresAt = null
    ): bool {
        $module = Module::where('code', $moduleCode)->first();
        if (!$module) {
            return false;
        }

        DB::table('user_permissions')->updateOrInsert(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
            ],
            array_merge(
                $permissions,
                [
                    'tenant_id' => $user->tenant_id,
                    'type' => 'grant',
                    'reason' => $reason,
                    'granted_by' => auth()->id(),
                    'expires_at' => $expiresAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            )
        );

        // Clear cache for this user
        $this->clearUserCache($user, $moduleCode);

        // Log the permission change
        $this->logPermissionChange(
            'user',
            $user->id,
            $module->id,
            null,
            $permissions,
            'grant',
            $reason
        );

        return true;
    }

    /**
     * Log permission changes for audit
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $moduleId
     * @param array|null $oldPermissions
     * @param array $newPermissions
     * @param string $action
     * @param string|null $reason
     */
    private function logPermissionChange(
        string $entityType,
        int $entityId,
        int $moduleId,
        ?array $oldPermissions,
        array $newPermissions,
        string $action,
        ?string $reason = null
    ): void {
        DB::table('permission_audit_logs')->insert([
            'tenant_id' => auth()->user()->tenant_id ?? 0,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'module_id' => $moduleId,
            'old_permissions' => $oldPermissions ? json_encode($oldPermissions) : null,
            'new_permissions' => json_encode($newPermissions),
            'action' => $action,
            'changed_by' => auth()->id() ?? 0,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
