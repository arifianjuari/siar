<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'name',
        'code',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot function dari model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            // Generate slug dari code jika slug belum diisi
            if (empty($module->slug) && !empty($module->code)) {
                $module->slug = Str::slug($module->code);
            }
        });

        static::updating(function ($module) {
            // Update slug jika code berubah dan slug belum diubah manual
            if ($module->isDirty('code') && !$module->isDirty('slug')) {
                $module->slug = Str::slug($module->code);
            }
        });
    }

    /**
     * Get tenants yang memiliki akses ke module ini
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Get tenant modules relationships
     */
    public function tenantModules()
    {
        return $this->hasMany(TenantModule::class);
    }

    /**
     * Get role module permissions
     */
    public function rolePermissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Get roles yang memiliki akses ke module ini
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_module_permissions')
            ->withPivot(['can_view', 'can_create', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }

    /**
     * Scope untuk module aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk module yang tersedia di tenant tertentu
     */
    public function scopeForTenant($query, $tenant)
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $query->whereHas('tenants', function ($q) use ($tenantId) {
            $q->where('tenants.id', $tenantId)
                ->where('tenant_modules.is_active', true);
        });
    }

    /**
     * Get slug attribute dengan safe value
     */
    public function getSlugAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->code)) {
            // Auto-generate slug from code if empty
            $slug = \Illuminate\Support\Str::slug($this->code);

            // Save if model exists in database
            if ($this->exists) {
                $this->slug = $slug;
                $this->save();
            }

            return $slug;
        }

        return 'undefined';
    }

    /**
     * Get icon_html attribute
     */
    public function getIconHtmlAttribute()
    {
        // Default icon if none is set
        $icon = $this->icon ?? 'fa-cube';
        return '<svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>';
    }
}
