<?php

namespace Modules\ActivityManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\Models\WorkUnit;

class ActivityAssignee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'activity_id',
        'assignee_type',
        'assignee_id',
        'role',
        'assigned_by',
    ];

    /**
     * Get the activity that owns the assignee.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the assigner (user who assigned).
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the assignee (user or work unit).
     */
    public function assignee(): MorphTo
    {
        return $this->morphTo('assignee', 'assignee_type', 'assignee_id');
    }

    /**
     * Handles mapping the assignee_type enum values to model class names.
     *
     * @param string $type
     * @return string
     */
    public function getMorphClass()
    {
        // Map the enum values to the appropriate model class names
        if ($this->assignee_type === 'user') {
            return \App\Models\User::class;
        } elseif ($this->assignee_type === 'work_unit') {
            return \App\Models\WorkUnit::class;
        }

        return $this->assignee_type;
    }

    /**
     * Get the formatted role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'responsible' => 'Penanggung Jawab (R)',
            'accountable' => 'Pemberi Kewenangan (A)',
            'consulted' => 'Konsultan (C)',
            'informed' => 'Penerima Informasi (I)',
            default => 'Lainnya'
        };
    }
}
