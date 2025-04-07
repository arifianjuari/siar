<?php

namespace App\Policies;

use App\Models\SPO;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class SPOPolicy
{
    use HandlesAuthorization;

    /**
     * Menentukan apakah user bisa melihat daftar SPO.
     */
    public function viewAny(User $user): bool
    {
        $canView = $user->hasPermission('work-units', 'can_view');
        Log::debug('SPOPolicy::viewAny', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'No Role',
            'result' => $canView
        ]);
        return $canView;
    }

    /**
     * Menentukan apakah user bisa melihat detail SPO.
     */
    public function view(User $user, SPO $spo): bool
    {
        // Pastikan user memiliki izin untuk melihat dan merupakan tenant yang sama
        $canView = $user->hasPermission('work-units', 'can_view') &&
            $user->tenant_id === $spo->tenant_id;
        Log::debug('SPOPolicy::view', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'No Role',
            'spo_id' => $spo->id,
            'user_tenant_id' => $user->tenant_id,
            'spo_tenant_id' => $spo->tenant_id,
            'has_permission' => $user->hasPermission('work-units', 'can_view'),
            'result' => $canView
        ]);
        return $canView;
    }

    /**
     * Menentukan apakah user bisa membuat SPO baru.
     */
    public function create(User $user): bool
    {
        $canCreate = $user->hasPermission('work-units', 'can_create');
        Log::debug('SPOPolicy::create', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'No Role',
            'result' => $canCreate
        ]);
        return $canCreate;
    }

    /**
     * Menentukan apakah user bisa mengubah SPO.
     */
    public function update(User $user, SPO $spo): bool
    {
        // Pastikan user memiliki izin untuk edit dan merupakan tenant yang sama
        $canEdit = $user->hasPermission('work-units', 'can_edit') &&
            $user->tenant_id === $spo->tenant_id;

        Log::debug('SPOPolicy::update', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'No Role',
            'spo_id' => $spo->id,
            'user_tenant_id' => $user->tenant_id,
            'spo_tenant_id' => $spo->tenant_id,
            'has_permission' => $user->hasPermission('work-units', 'can_edit'),
            'result' => $canEdit
        ]);

        // Untuk debugging: selalu izinkan pada mode debugging
        if (config('app.debug') === true) {
            return true;
        }
        return $canEdit;
    }

    /**
     * Menentukan apakah user bisa menghapus SPO.
     */
    public function delete(User $user, SPO $spo): bool
    {
        // Pastikan user memiliki izin untuk hapus dan merupakan tenant yang sama
        $canDelete = $user->hasPermission('work-units', 'can_delete') &&
            $user->tenant_id === $spo->tenant_id;

        Log::debug('SPOPolicy::delete', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_id' => $user->role_id,
            'role_name' => $user->role->name ?? 'No Role',
            'spo_id' => $spo->id,
            'user_tenant_id' => $user->tenant_id,
            'spo_tenant_id' => $spo->tenant_id,
            'has_permission' => $user->hasPermission('work-units', 'can_delete'),
            'result' => $canDelete
        ]);

        // Untuk debugging: selalu izinkan pada mode debugging
        if (config('app.debug') === true) {
            return true;
        }
        return $canDelete;
    }

    /**
     * Menentukan apakah user bisa memulihkan SPO yang dihapus.
     */
    public function restore(User $user, SPO $spo): bool
    {
        // Pastikan user memiliki izin untuk edit dan merupakan tenant yang sama
        return $user->hasPermission('work-units', 'can_edit') &&
            $user->tenant_id === $spo->tenant_id;
    }

    /**
     * Menentukan apakah user bisa menghapus permanen SPO.
     */
    public function forceDelete(User $user, SPO $spo): bool
    {
        // Hanya superadmin yang bisa menghapus permanen 
        return $user->hasRole('superadmin') &&
            $user->tenant_id === $spo->tenant_id;
    }
}
