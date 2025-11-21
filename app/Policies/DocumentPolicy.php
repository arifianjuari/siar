<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use App\Services\PermissionService;

class DocumentPolicy extends BasePolicy
{
    /**
     * Module code for document management
     */
    protected string $moduleCode = 'document-management';

    /**
     * Additional authorization for document download
     */
    public function download(User $user, Document $document): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $document)) {
            return false;
        }

        // User needs view permission to download
        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_view');
    }

    /**
     * Check if user can share the document
     */
    public function share(User $user, Document $document): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $document)) {
            return false;
        }

        // Sharing requires either edit permission or being the owner
        if ($document->created_by === $user->id) {
            return true;
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_edit');
    }

    /**
     * Check if user can approve the document
     */
    public function approve(User $user, Document $document): bool
    {
        // Check tenant isolation first
        if (!$this->checkTenantAccess($user, $document)) {
            return false;
        }

        // Approval requires edit permission and cannot be done by creator
        if ($document->created_by === $user->id) {
            return false; // Creator cannot approve their own document
        }

        return $this->permissionService->userHasPermission($user, $this->getModuleCode(), 'can_edit');
    }
}
