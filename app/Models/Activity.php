<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'uuid',
        'tenant_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'due_date',
        'category',
        'status',
        'priority',
        'progress_percentage',
        'work_unit_id',
        'parent_id',
        'created_by',
        'updated_by',
        'completed_by',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'tags'
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'progress_percentage' => 'integer',
        'tags' => 'json',
    ];

    /**
     * Boot function dari model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Get tenant yang memiliki aktivitas ini
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get work unit yang terkait dengan aktivitas ini
     */
    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * Get user yang membuat aktivitas ini
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get user yang terakhir mengupdate aktivitas ini
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get user yang menyelesaikan aktivitas ini
     */
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get user yang membatalkan aktivitas ini
     */
    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get aktivitas induk
     */
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    /**
     * Get aktivitas anak/sub-aktivitas
     */
    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    /**
     * Get assignees untuk aktivitas ini
     */
    public function assignees()
    {
        return $this->hasMany(ActivityAssignee::class);
    }

    /**
     * Get user yang ditugaskan pada aktivitas ini
     */
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'activity_assignees', 'activity_id', 'assignee_id')
            ->where('assignee_type', 'user')
            ->withPivot('role', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get work unit yang ditugaskan pada aktivitas ini
     */
    public function assignedWorkUnits()
    {
        return $this->belongsToMany(WorkUnit::class, 'activity_assignees', 'activity_id', 'assignee_id')
            ->where('assignee_type', 'work_unit')
            ->withPivot('role', 'assigned_by')
            ->withTimestamps();
    }

    /**
     * Get item yang dapat ditindaklanjuti dari aktivitas ini
     */
    public function actionableItems()
    {
        return $this->hasMany(ActionableItem::class);
    }

    /**
     * Get log status aktivitas ini
     */
    public function statusLogs()
    {
        return $this->hasMany(ActivityStatusLog::class);
    }

    /**
     * Get komentar aktivitas ini
     */
    public function comments()
    {
        return $this->hasMany(ActivityComment::class);
    }

    /**
     * Accessor untuk warna berdasarkan status
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'planned' => 'info',
            'pending' => 'warning',
            'ongoing' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Accessor untuk warna berdasarkan prioritas
     */
    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'critical' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Accessor untuk label status
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'planned' => 'Direncanakan',
            'pending' => 'Tertunda',
            'ongoing' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }

    /**
     * Accessor untuk label prioritas
     */
    public function getPriorityLabelAttribute()
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis',
            default => ucfirst($this->priority)
        };
    }

    /**
     * Accessor untuk progres dalam format persen
     */
    public function getProgressPercentFormattedAttribute()
    {
        return $this->progress_percentage . '%';
    }

    /**
     * Scope untuk kegiatan yang belum selesai
     */
    public function scopeUnfinished($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope untuk kegiatan dengan prioritas tinggi
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    /**
     * Scope untuk kegiatan yang hampir jatuh tempo
     */
    public function scopeNearingDueDate($query, $days = 7)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', now()->addDays($days))
            ->whereDate('due_date', '>=', now());
    }

    /**
     * Scope untuk kegiatan yang telah melewati jatuh tempo
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now());
    }
}
