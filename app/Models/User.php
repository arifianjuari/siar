<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'created_by',
        'updated_by',
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
     * Cek apakah user memiliki role tertentu
     */
    public function hasRole(string $roleSlug): bool
    {
        return $this->role && $this->role->slug === $roleSlug;
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
}
