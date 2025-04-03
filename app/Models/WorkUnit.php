<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkUnit extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'unit_code',
        'unit_name',
        'unit_type',
        'head_of_unit_id',
        'description',
        'is_active',
        'parent_id',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get the tenant that owns the work unit.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the head of the unit for the work unit.
     */
    public function headOfUnit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_unit_id');
    }

    /**
     * Get the parent work unit.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class, 'parent_id');
    }

    /**
     * Get the children work units.
     */
    public function children()
    {
        return $this->hasMany(WorkUnit::class, 'parent_id');
    }

    /**
     * Get the risk reports for the work unit.
     */
    public function riskReports()
    {
        return $this->hasMany(RiskReport::class, 'work_unit_id');
    }

    /**
     * Get the correspondences for the work unit.
     */
    public function correspondences()
    {
        return $this->hasMany(Correspondence::class, 'work_unit_id');
    }

    /**
     * Scope a query to only include active work units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include work units belonging to a given tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
