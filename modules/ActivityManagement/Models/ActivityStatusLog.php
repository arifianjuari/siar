<?php

namespace Modules\ActivityManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ActivityStatusLog extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'activity_id',
        'changed_by',
        'log_type',
        'from_value',
        'to_value',
        'note',
        'created_at'
    ];

    /**
     * Mematikan timestamps (hanya menggunakan created_at)
     */
    public $timestamps = false;

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get aktivitas yang terkait dengan log
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get user yang melakukan perubahan
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get label untuk tipe log
     */
    public function getLogTypeLabelAttribute()
    {
        return match ($this->log_type) {
            'status_changed' => 'Perubahan Status',
            'assignee_change' => 'Perubahan Penugasan',
            'progress_update' => 'Pembaruan Progres',
            'comment_added' => 'Penambahan Komentar',
            'document_attached' => 'Lampiran Dokumen',
            default => $this->log_type
        };
    }

    /**
     * Get deskripsi perubahan
     */
    public function getChangeDescriptionAttribute()
    {
        $description = '';

        switch ($this->log_type) {
            case 'status_changed':
                $description = "Status berubah dari '{$this->from_value}' menjadi '{$this->to_value}'";
                break;
            case 'assignee_change':
                $description = "Perubahan penugasan: {$this->to_value}";
                break;
            case 'progress_update':
                $description = "Progres diperbarui dari {$this->from_value}% menjadi {$this->to_value}%";
                break;
            default:
                $description = "{$this->from_value} → {$this->to_value}";
        }

        return $description;
    }
}
