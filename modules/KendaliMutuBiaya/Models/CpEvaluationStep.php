<?php

namespace Modules\KendaliMutuBiaya\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class CpEvaluationStep extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    /**
     * Relasi ke evaluasi
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(CpEvaluation::class, 'cp_evaluation_id');
    }

    /**
     * Relasi ke step yang dievaluasi
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(CpStep::class, 'cp_step_id');
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
     * Scope untuk langkah yang sudah dilakukan
     */
    public function scopeDone($query)
    {
        return $query->where('is_done', true);
    }

    /**
     * Scope untuk langkah yang belum dilakukan
     */
    public function scopeNotDone($query)
    {
        return $query->where('is_done', false);
    }
}
