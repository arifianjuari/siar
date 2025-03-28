<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAnalysis extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'risk_analysis';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'risk_report_id',
        'direct_cause',
        'root_cause',
        'contributor_factors',
        'recommendation_short',
        'recommendation_medium',
        'recommendation_long',
        'analyzed_by',
        'analyzed_at',
        'analysis_status',
    ];

    /**
     * Atribut yang harus di-cast ke tipe native.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contributor_factors' => 'array',
        'analyzed_at' => 'datetime',
    ];

    /**
     * Relasi ke model RiskReport.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function riskReport(): BelongsTo
    {
        return $this->belongsTo(RiskReport::class, 'risk_report_id');
    }

    /**
     * Relasi ke model User untuk analyst.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function analyst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }

    /**
     * Mendapatkan label status analisis.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->analysis_status) {
            'draft' => 'Draft',
            'in_progress' => 'Ditinjau',
            'completed' => 'Selesai',
            'reviewed' => 'Ditinjau',
            default => 'Tidak Diketahui'
        };
    }
}
