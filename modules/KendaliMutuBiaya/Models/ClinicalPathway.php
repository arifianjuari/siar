<?php

namespace Modules\KendaliMutuBiaya\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Tenant;
use App\Models\User;

class ClinicalPathway extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'structured_data' => 'array',
    ];

    /**
     * Relasi ke tenant yang memiliki clinical pathway
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relasi ke pengguna yang membuat data
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke pengguna yang terakhir mengupdate data
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke langkah-langkah dalam clinical pathway
     */
    public function steps(): HasMany
    {
        return $this->hasMany(CpStep::class);
    }

    /**
     * Relasi ke tarif dalam clinical pathway
     */
    public function tariff(): HasOne
    {
        return $this->hasOne(CpTariff::class);
    }

    /**
     * Relasi ke beberapa tarif dalam clinical pathway
     */
    public function tariffs(): HasMany
    {
        return $this->hasMany(CpTariff::class);
    }

    /**
     * Relasi ke evaluasi clinical pathway
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(CpEvaluation::class);
    }

    /**
     * Scope untuk clinical pathway yang aktif/published
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope untuk clinical pathway milik tenant tertentu
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
