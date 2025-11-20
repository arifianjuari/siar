<?php

namespace Modules\KendaliMutuBiaya\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class CpStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Relasi ke clinical pathway
     */
    public function clinicalPathway(): BelongsTo
    {
        return $this->belongsTo(ClinicalPathway::class);
    }

    /**
     * Relasi ke evaluation steps
     */
    public function evaluationSteps(): HasMany
    {
        return $this->hasMany(CpEvaluationStep::class);
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
     * Scope untuk mengurutkan berdasarkan urutan langkah
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('step_category', $category);
    }
}
