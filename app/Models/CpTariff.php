<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpTariff extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'claim_value' => 'decimal:2',
    ];

    /**
     * Relasi ke clinical pathway
     */
    public function clinicalPathway(): BelongsTo
    {
        return $this->belongsTo(ClinicalPathway::class);
    }

    /**
     * Relasi ke user yang membuat data
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengupdate data
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk mencari berdasarkan kode INA-CBG
     */
    public function scopeByInaCbgCode($query, $code)
    {
        return $query->where('code_ina_cbg', $code);
    }

    /**
     * Scope untuk mencari berdasarkan nilai klaim
     */
    public function scopeWithMinimumClaim($query, $value)
    {
        return $query->where('claim_value', '>=', $value);
    }
}
