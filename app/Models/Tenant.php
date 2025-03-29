<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Tenant extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Boot model untuk menambahkan hooks dan event listener
     */
    protected static function boot()
    {
        parent::boot();

        // Mencegah penghapusan tenant yang masih memiliki user
        static::deleting(function (Tenant $tenant) {
            // Cek jika tenant adalah system tenant
            if ($tenant->domain === 'system') {
                throw new \Exception('Tenant System tidak dapat dihapus karena berisi user superadmin.');
            }

            // Cek jika tenant masih memiliki user
            if ($tenant->users()->count() > 0) {
                throw new \Exception('Tenant tidak dapat dihapus karena masih memiliki pengguna. Hapus semua pengguna terlebih dahulu.');
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'domain',
        'database',
        'description',
        'address',
        'phone',
        'email',
        'logo',
        'settings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi dengan module melalui tenant_modules
     */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /**
     * Relasi dengan role
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Relasi dengan user
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi dengan work units (unit kerja)
     */
    public function workUnits(): HasMany
    {
        return $this->hasMany(WorkUnit::class);
    }

    /**
     * Relasi dengan tags
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Ambil modul yang aktif untuk tenant ini
     */
    public function activeModules()
    {
        return $this->modules()->wherePivot('is_active', true);
    }

    /**
     * Cek apakah tenant memiliki modul tertentu yang aktif
     */
    public function hasModule($moduleCode)
    {
        return $this->activeModules()
            ->where('modules.code', $moduleCode)
            ->exists();
    }

    /**
     * Aktifkan modul untuk tenant ini
     */
    public function activateModule($moduleId)
    {
        $this->modules()->syncWithoutDetaching([
            $moduleId => ['is_active' => true]
        ]);

        return $this;
    }

    /**
     * Nonaktifkan modul untuk tenant ini
     */
    public function deactivateModule($moduleId)
    {
        $this->modules()->syncWithoutDetaching([
            $moduleId => ['is_active' => false]
        ]);

        return $this;
    }

    /**
     * Scope untuk tenant yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Konfigurasi activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'domain', 'database', 'description', 'address', 'phone', 'email', 'logo', 'is_active', 'settings'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                return "Tenant telah {$eventName}";
            });
    }

    /**
     * Tambahkan properti ke log aktivitas
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'tenant_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
