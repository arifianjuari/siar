<?php

namespace Modules\ActivityManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Document;
use App\Models\Task;

class ActionableItem extends Model
{
    use HasFactory;

    /**
     * Boot the model and auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'uuid',
        'activity_id',
        'actionable_type',
        'actionable_id',
        'title',
        'description',
        'due_date',
        'priority',
        'order',
        'completed',
        'completed_by',
        'completed_at',
        'action_type',
        'reference',
        'is_mandatory',
        'status',
        'note',
        'created_by',
        'updated_by'
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get aktivitas yang terkait dengan item
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get entitas yang harus melakukan tindakan
     */
    public function actionable()
    {
        return $this->morphTo();
    }

    /**
     * Get user yang membuat item
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get user yang mengupdate item
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get user yang menyelesaikan item
     */
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get waktu relatif deadline (untuk tampilan)
     */
    public function getTimeRemainingAttribute()
    {
        if (!$this->due_date) {
            return 'Tidak ada tenggat';
        }

        if ($this->completed) {
            return 'Selesai';
        }

        $now = now();
        $dueDate = $this->due_date;

        if ($dueDate->isPast()) {
            return 'Terlambat ' . $now->diffInDays($dueDate) . ' hari';
        }

        if ($dueDate->isToday()) {
            return 'Hari ini';
        }

        if ($dueDate->isTomorrow()) {
            return 'Besok';
        }

        $diffDays = $now->diffInDays($dueDate);

        if ($diffDays <= 7) {
            return $diffDays . ' hari lagi';
        }

        return $dueDate->format('d M Y');
    }

    /**
     * Get detail dari item tindakan
     * Mengembalikan informasi yang sesuai berdasarkan tipe actionable
     */
    public function getDetailAttribute()
    {
        // Default data
        $data = [
            'title' => $this->title ?? 'Item Tindakan',
            'description' => $this->description ?? 'Tidak ada deskripsi',
            'icon' => 'fa-check-circle'
        ];

        // Jika memiliki actionable (polymorphic relation)
        if ($this->actionable) {
            if ($this->actionable_type == 'App\\Models\\Document') {
                $data['title'] = $this->actionable->document_title ?? $this->title;
                $data['description'] = $this->description ?? ('Dokumen: ' . $this->actionable->document_number);
                $data['icon'] = 'fa-file-alt';
            } elseif ($this->actionable_type == 'App\\Models\\Task') {
                $data['title'] = $this->actionable->task_name ?? $this->title;
                $data['description'] = $this->description ?? $this->actionable->task_description;
                $data['icon'] = 'fa-tasks';
            }
        }

        return $data;
    }

    /**
     * Get label status yang readable untuk tampilan
     */
    public function getStatusLabelAttribute()
    {
        $statusMap = [
            'pending' => 'Menunggu',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $statusMap[$this->status] ?? 'Tidak Diketahui';
    }

    /**
     * Get warna status untuk tampilan
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'secondary'
        ];

        return $colorMap[$this->status] ?? 'secondary';
    }

    /**
     * Get label prioritas yang readable untuk tampilan
     */
    public function getPriorityLabelAttribute()
    {
        $priorityMap = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis'
        ];

        return $priorityMap[$this->priority] ?? 'Tidak Diketahui';
    }

    /**
     * Get warna prioritas untuk tampilan
     */
    public function getPriorityColorAttribute()
    {
        $colorMap = [
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'critical' => 'danger'
        ];

        return $colorMap[$this->priority] ?? 'secondary';
    }
}
