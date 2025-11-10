<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpEvaluationAdditionalStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'additional_step_cost' => 'decimal:2',
    ];

    /**
     * Relasi ke evaluasi
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(CpEvaluation::class, 'cp_evaluation_id');
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
     * Scope untuk langkah dengan justifikasi
     */
    public function scopeJustified($query)
    {
        return $query->where('justification_status', 'Justified');
    }

    /**
     * Scope untuk langkah tanpa justifikasi
     */
    public function scopeUnjustified($query)
    {
        return $query->where('justification_status', 'Tidak Justified');
    }

    /**
     * Scope untuk mencari biaya tambahan di atas nilai tertentu
     */
    public function scopeWithCostAbove($query, $value)
    {
        return $query->where('additional_step_cost', '>', $value);
    }
}
