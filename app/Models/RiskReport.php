<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class RiskReport extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'risk_title',
        'chronology',
        'reporter_unit',
        'risk_type',
        'risk_category',
        'occurred_at',
        'impact',
        'probability',
        'risk_level',
        'status',
        'recommendation',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'occurred_at' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that reviewed the risk report.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user that approved the risk report.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
