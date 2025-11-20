<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'role_id',
        'work_unit_id',
        'supervisor_id',
        'employment_status',
        'name',
        'email',
        'profile_photo',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'created_by',
        'updated_by',
        'position',
        'rank',
        'nrp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'employment_status' => 'string',
    ];

    /**
     * Relasi dengan tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relasi dengan role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi dengan work unit
     */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * Relasi dengan supervisor (atasan)
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Relasi dengan subordinates (bawahan)
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Relasi dengan user yang membuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan user yang terakhir mengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Cek apakah user adalah superadmin
     * 
     * @return bool
     */
    public function isSuperadmin(): bool
    {
        return $this->role && 
               (strtolower($this->role->name) === 'super admin' || 
                strtolower($this->role->slug) === 'super-admin');
    }

    /**
     * Cek apakah user memiliki role tertentu
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && strtolower($this->role->slug) === strtolower($roleSlug);
    }

    /**
     * Periksa apakah user memiliki izin untuk modul tertentu
     */
    public function hasPermission($module, $permission = 'can_view'): bool
    {
        try {
            // Jika modul atau role null, return false
            if (!$module || !$this->role) {
                \Illuminate\Support\Facades\Log::debug("User::hasPermission: modul atau role null", [
                    'user_id' => $this->id,
                    'user_name' => $this->name,
                    'module' => $module,
                    'role_id' => $this->role_id,
                    'permission' => $permission
                ]);
                return false;
            }

            $moduleId = $module instanceof \App\Models\Module ? $module->id : $module;

            // Cek apakah role memiliki izin untuk modul tersebut
            $rolePermission = $this->role->modulePermissions()
                ->where('module_id', $moduleId)
                ->first();

            if (!$rolePermission) {
                \Illuminate\Support\Facades\Log::debug("User::hasPermission: role tidak memiliki permission untuk modul", [
                    'user_id' => $this->id,
                    'role_id' => $this->role_id,
                    'role_name' => $this->role->name,
                    'module_id' => $moduleId,
                    'permission' => $permission
                ]);
                return false;
            }

            // Periksa jenis izin yang diminta
            $hasSpecificPermission = $rolePermission->pivot && $rolePermission->pivot->$permission;

            \Illuminate\Support\Facades\Log::debug("User::hasPermission result: " . ($hasSpecificPermission ? 'true' : 'false'), [
                'user_id' => $this->id,
                'user_name' => $this->name,
                'role_id' => $this->role_id,
                'role_name' => $this->role->name,
                'module_id' => $moduleId,
                'permission' => $permission,
                'permission_value' => $rolePermission->pivot->$permission ?? null
            ]);

            return $hasSpecificPermission;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("User::hasPermission error: " . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $this->id,
                'module' => $module instanceof \App\Models\Module ? $module->id : $module,
                'permission' => $permission
            ]);
            return false;
        }
    }

    /**
     * Konfigurasi activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'role_id', 'tenant_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                return "User telah {$eventName}";
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
     * Check if user has access to a specific tenant
     * 
     * @param int $tenantId
     * @return bool
     */
    public function hasAccessToTenant(int $tenantId): bool
    {
        // Superadmin has access to all tenants
        if ($this->isSuperadmin()) {
            return true;
        }

        // User can only access their own tenant
        return $this->tenant_id === $tenantId;
    }

    /**
     * Switch user's current tenant context (with validation)
     * 
     * @param int $tenantId
     * @throws \Exception
     */
    public function switchTenant(int $tenantId): void
    {
        // Validate user has access to the target tenant
        if (!$this->hasAccessToTenant($tenantId)) {
            \Illuminate\Support\Facades\Log::warning('Unauthorized tenant switch attempt', [
                'user_id' => $this->id,
                'user_email' => $this->email,
                'from_tenant' => $this->tenant_id,
                'to_tenant' => $tenantId,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            throw new \Exception('User tidak memiliki akses ke tenant ini');
        }

        // Validate target tenant exists and is active
        $targetTenant = Tenant::where('id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (!$targetTenant) {
            throw new \Exception('Tenant tidak ditemukan atau tidak aktif');
        }

        // Log successful tenant switch for audit
        \Illuminate\Support\Facades\Log::info('Tenant switch successful', [
            'user_id' => $this->id,
            'user_email' => $this->email,
            'from_tenant' => $this->tenant_id,
            'to_tenant' => $tenantId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update session with new tenant
        session(['tenant_id' => $tenantId]);

        // Clear user's permission cache after tenant switch
        if (app()->bound(\App\Services\PermissionService::class)) {
            app(\App\Services\PermissionService::class)->clearUserCache($this);
        }
    }
}
