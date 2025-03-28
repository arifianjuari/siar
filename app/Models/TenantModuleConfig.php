<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class TenantModuleConfig extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * Nama tabel
     */
    protected $table = 'tenant_module_configs';

    /**
     * Atribut yang dapat diisi
     */
    protected $fillable = [
        'tenant_id',
        'module',
        'feature',
        'config_key',
        'config_value',
        'allowed_roles',
    ];

    /**
     * Atribut yang dikonversi tipe datanya
     */
    protected $casts = [
        'allowed_roles' => 'array',
        'config_value' => 'array',
    ];

    /**
     * Mendapatkan tenant yang memiliki konfigurasi ini
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
