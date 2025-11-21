<?php

namespace Modules\KendaliMutuBiaya\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class CpEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'evaluation_date' => 'date',
        'compliance_percentage' => 'decimal:2',
        'total_additional_cost' => 'decimal:2',
    ];

    /**
     * Relasi ke clinical pathway yang dievaluasi
     */
    public function clinicalPathway(): BelongsTo
    {
        return $this->belongsTo(ClinicalPathway::class);
    }

    /**
     * Relasi ke user evaluator
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
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
     * Relasi ke langkah evaluasi
     */
    public function evaluationSteps(): HasMany
    {
        return $this->hasMany(CpEvaluationStep::class);
    }

    /**
     * Relasi ke langkah tambahan
     */
    public function additionalSteps(): HasMany
    {
        return $this->hasMany(CpEvaluationAdditionalStep::class);
    }

    /**
     * Scope untuk status evaluasi Hijau
     */
    public function scopeGreen($query)
    {
        return $query->where('evaluation_status', 'Hijau');
    }

    /**
     * Scope untuk status evaluasi Kuning
     */
    public function scopeYellow($query)
    {
        return $query->where('evaluation_status', 'Kuning');
    }

    /**
     * Scope untuk status evaluasi Merah
     */
    public function scopeRed($query)
    {
        return $query->where('evaluation_status', 'Merah');
    }

    /**
     * Scope untuk tingkat kepatuhan di atas nilai tertentu
     */
    public function scopeWithMinCompliance($query, $percentage)
    {
        return $query->where('compliance_percentage', '>=', $percentage);
    }
}
