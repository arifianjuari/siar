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
        'requested_at',
        'requested_by',
        'approved_at',
        'approved_by',
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'is_active' => 'boolean',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
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
     * Get user yang meminta modul
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get user yang menyetujui modul
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk tenant module aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk tenant module yang memiliki permintaan aktif
     * (sudah diminta tapi belum diapprove)
     */
    public function scopePendingRequests($query)
    {
        return $query->whereNotNull('requested_at')
            ->whereNull('approved_at')
            ->where('is_active', false);
    }
}
