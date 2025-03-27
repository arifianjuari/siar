<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
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
     * Get the parent work unit.
     */
    public function parent()
    {
        return $this->belongsTo(WorkUnit::class, 'parent_id');
    }

    /**
     * Get the child work units.
     */
    public function children()
    {
        return $this->hasMany(WorkUnit::class, 'parent_id')->orderBy('order');
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
