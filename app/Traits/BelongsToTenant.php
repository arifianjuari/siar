<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait
     */
    protected static function bootBelongsToTenant()
    {
        // Add global scope for tenant filtering
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            $tenantId = static::getCurrentTenantId();
            
            if ($tenantId) {
                // Use fully qualified table name to avoid ambiguity
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.tenant_id', $tenantId);
            }
        });

        // Auto-fill tenant_id on create
        static::creating(function ($model) {
            if (!$model->isDirty('tenant_id')) {
                $tenantId = static::getCurrentTenantId();
                
                if ($tenantId) {
                    $model->tenant_id = $tenantId;
                } elseif (!app()->runningInConsole()) {
                    // Log warning if no tenant context in web request
                    Log::warning('Creating model without tenant context', [
                        'model' => get_class($model),
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        });
        
        // Validate tenant_id on save/update
        static::saving(function ($model) {
            if ($model->exists && $model->isDirty('tenant_id')) {
                $originalTenantId = $model->getOriginal('tenant_id');
                if ($originalTenantId && $originalTenantId !== $model->tenant_id) {
                    Log::alert('Attempt to change tenant_id on existing model', [
                        'model' => get_class($model),
                        'model_id' => $model->id,
                        'original_tenant_id' => $originalTenantId,
                        'new_tenant_id' => $model->tenant_id,
                        'user_id' => Auth::id(),
                    ]);
                    // Prevent changing tenant_id on existing records
                    $model->tenant_id = $originalTenantId;
                }
            }
        });
    }

    /**
     * Get the current tenant ID from multiple sources
     * Priority: Auth User > Session > Request
     */
    protected static function getCurrentTenantId(): ?int
    {
        // Skip tenant filtering for console commands unless explicitly set
        if (app()->runningInConsole()) {
            return static::getConsoleTenantId();
        }

        // Priority 1: Get from authenticated user
        // Use getAttributeValue() untuk menghindari eager loading yang menyebabkan infinite loop
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && isset($user->tenant_id)) {
                return $user->tenant_id;
            }
        }

        // Priority 2: Get from session (fallback for guest access)
        if (app()->bound('session') && session()->has('tenant_id')) {
            return session('tenant_id');
        }

        // Priority 3: Get from request (if set by middleware)
        if (request()->has('__tenant_id')) {
            return request()->get('__tenant_id');
        }

        return null;
    }

    /**
     * Get tenant ID for console commands
     */
    protected static function getConsoleTenantId(): ?int
    {
        // Allow setting tenant context for console commands via environment
        if (env('CONSOLE_TENANT_ID')) {
            return (int) env('CONSOLE_TENANT_ID');
        }
        
        return null;
    }

    /**
     * Relationship to tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope query to specific tenant
     */
    public function scopeTenant($query, $tenantId)
    {
        // Use fully qualified table name to avoid ambiguity
        $table = $query->getModel()->getTable();
        return $query->where($table . '.tenant_id', $tenantId);
    }

    /**
     * Scope to remove tenant filtering (use with caution!)
     * 
     * WARNING: This bypasses tenant isolation and should only be used
     * by superadmins or in specific controlled scenarios.
     */
    public function scopeWithoutTenant($query)
    {
        // Log the removal of tenant scope for security audit
        \Illuminate\Support\Facades\Log::warning('Tenant scope removed', [
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'user_email' => \Illuminate\Support\Facades\Auth::user()?->email,
            'model' => get_class($query->getModel()),
            'table' => $query->getModel()->getTable(),
            'ip' => request()?->ip(),
            'url' => request()?->fullUrl(),
            'stack_trace' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                ->map(fn($trace) => ($trace['file'] ?? '') . ':' . ($trace['line'] ?? '') . ' ' . ($trace['function'] ?? ''))
                ->filter()
                ->take(5)
                ->toArray(),
        ]);

        return $query->withoutGlobalScope('tenant_id');
    }

    /**
     * Check if model belongs to current tenant
     */
    public function belongsToCurrentTenant(): bool
    {
        $currentTenantId = static::getCurrentTenantId();
        return $currentTenantId && $this->tenant_id === $currentTenantId;
    }

    /**
     * Validate that model belongs to specified tenant
     */
    public function validateTenantAccess($tenantId): bool
    {
        return $this->tenant_id === $tenantId;
    }
}
