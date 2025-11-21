<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_active',
        'parent_role_id',
        'level',
        'inherit_permissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'inherit_permissions' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Maximum depth for role hierarchy
     */
    const MAX_HIERARCHY_DEPTH = 10;

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate circular dependency before saving
        static::saving(function ($role) {
            if ($role->parent_role_id) {
                // Check for circular dependency
                $ancestors = static::getAncestors($role->parent_role_id);
                if (in_array($role->id, $ancestors)) {
                    throw new \Exception('Circular dependency detected in role hierarchy');
                }

                // Check hierarchy depth
                if (count($ancestors) >= static::MAX_HIERARCHY_DEPTH) {
                    throw new \Exception('Role hierarchy depth exceeds maximum limit of ' . static::MAX_HIERARCHY_DEPTH);
                }

                // Set the level based on parent
                $parent = static::find($role->parent_role_id);
                $role->level = ($parent ? $parent->level : 0) + 1;
            } else {
                $role->level = 0;
            }
        });
    }

    /**
     * Get all ancestors of a role
     */
    public static function getAncestors($roleId, $ancestors = [])
    {
        $role = static::find($roleId);
        if (!$role || !$role->parent_role_id) {
            return $ancestors;
        }

        // Prevent infinite loop
        if (in_array($role->parent_role_id, $ancestors)) {
            return $ancestors;
        }

        $ancestors[] = $role->parent_role_id;
        return static::getAncestors($role->parent_role_id, $ancestors);
    }

    /**
     * Relationship with parent role
     */
    public function parentRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'parent_role_id');
    }

    /**
     * Relationship with child roles
     */
    public function childRoles(): HasMany
    {
        return $this->hasMany(Role::class, 'parent_role_id');
    }

    /**
     * Get all descendant roles (recursive)
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        $children = $this->childRoles;

        foreach ($children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Ambil semua permission yang terhubung dengan role ini
     */
    public function permissions()
    {
        return $this->hasMany(RoleModulePermission::class);
    }

    /**
     * Ambil semua user yang memiliki role ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Cek apakah role memiliki permission pada modul tertentu
     */
    public function hasPermission($moduleCode, $permission = 'can_view')
    {
        return $this->permissions()
            ->whereHas('module', function ($query) use ($moduleCode) {
                $query->where('code', $moduleCode);
            })
            ->where($permission, true)
            ->exists();
    }

    /**
     * Dapatkan semua permission role untuk modul tertentu
     */
    public function getModulePermissions($moduleId)
    {
        return $this->permissions()
            ->where('module_id', $moduleId)
            ->first();
    }

    /**
     * Get modules dengan permissions
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'role_module_permissions')
            ->withPivot(['can_view', 'can_create', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }

    /**
     * Scope untuk role aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relasi dengan tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Konfigurasi activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'is_active', 'tenant_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                return "Role {$this->name} telah {$eventName}";
            });
    }

    /**
     * Tambahkan tenant_id ke log aktivitas
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'tenant_id' => $this->tenant_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get module permissions for this role
     */
    public function modulePermissions()
    {
        return $this->belongsToMany(\App\Models\Module::class, 'role_module_permissions')
            ->withPivot(['can_view', 'can_create', 'can_edit', 'can_delete'])
            ->withTimestamps();
    }
}
