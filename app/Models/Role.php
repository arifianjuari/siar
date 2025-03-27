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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

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
