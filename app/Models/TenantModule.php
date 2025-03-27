<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantModule extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * Nama tabel
     */
    protected $table = 'tenant_modules';

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'tenant_id',
        'module_id',
        'is_active',
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get tenant relatif ke tenant module
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get module relatif ke tenant module
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Scope untuk tenant module aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
