<?php

namespace App\Policies;

use App\Models\RiskAnalysis;
use App\Models\RiskReport;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RiskAnalysisPolicy extends BasePolicy
{

    /**
     * Module code for risk management
     */
    protected string $moduleCode = 'risk-management';

    /**
     * Override before method to add logging
     */
    public function before(User $user, string $ability)
    {
        // Log untuk debug
        Log::info('RiskAnalysisPolicy before check', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_slug' => $user->role ? $user->role->slug : 'no_role',
            'ability' => $ability
        ]);

        // Use parent's before method (handles superadmin and module access)
        return parent::before($user, $ability);
    }

    /**
     * Determine if user can view any risk analysis
     * Uses BasePolicy with custom permission check
     */
    public function viewAny(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->moduleCode, 'can_view');
    }

    /**
     * Determine if user can view specific risk analysis
     * Adds tenant check on top of base permission
     */
    public function view(User $user, $model): bool
    {
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_view')) {
            return false;
        }

        // Ensure tenant isolation
        return !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
    }

    /**
     * Determine if user can create risk analysis
     * Custom logic: check if report already has analysis
     */
    public function create(User $user): bool
    {
        return $this->permissionService->userHasPermission($user, $this->moduleCode, 'can_create');
    }

    /**
     * Custom method to check if user can create analysis for specific report
     */
    public function createForReport(User $user, RiskReport $report): bool
    {
        Log::info('RiskAnalysisPolicy createForReport check', [
            'user_id' => $user->id,
            'report_id' => $report->id
        ]);

        // Check base permission
        if (!$this->create($user)) {
            Log::warning('RiskAnalysisPolicy: denied, no permission', ['user_id' => $user->id]);
            return false;
        }

        // Check if report already has analysis
        if ($report->analysis) {
            Log::warning('RiskAnalysisPolicy: denied, report already has analysis', [
                'user_id' => $user->id,
                'report_id' => $report->id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Determine if user can update risk analysis
     * Custom logic: only analyst who created it or admin can edit
     */
    public function update(User $user, $model): bool
    {
        // Check base permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_edit')) {
            return false;
        }

        // Tenant isolation check
        if (isset($model->tenant_id) && $user->tenant_id !== $model->tenant_id) {
            return false;
        }

        // Only analyst who created it or admin can edit
        $isAnalyst = isset($model->analyzed_by) && $model->analyzed_by === $user->id;
        return $isAnalyst || $user->isSuperadmin();
    }

    /**
     * Determine if user can delete risk analysis
     */
    public function delete(User $user, $model): bool
    {
        // Check base permission
        if (!$this->permissionService->userHasPermission($user, $this->moduleCode, 'can_delete')) {
            return false;
        }

        // Tenant isolation check
        return !isset($model->tenant_id) || $user->tenant_id === $model->tenant_id;
    }

}
