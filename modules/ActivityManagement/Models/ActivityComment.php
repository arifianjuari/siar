<?php

namespace Modules\ActivityManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ActivityComment extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'activity_id',
        'user_id',
        'comment',
        'parent_id',
        'attachments'
    ];

    /**
     * Atribut yang dikonversi ke tipe data
     */
    protected $casts = [
        'attachments' => 'json',
    ];

    /**
     * Get aktivitas yang terkait dengan komentar
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get user yang membuat komentar
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get komentar induk
     */
    public function parent()
    {
        return $this->belongsTo(ActivityComment::class, 'parent_id');
    }

    /**
     * Get komentar balasan
     */
    public function replies()
    {
        return $this->hasMany(ActivityComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Scope komentar induk (tanpa parent)
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check apakah komentar memiliki lampiran
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * Get jumlah balasan
     */
    public function getReplyCountAttribute()
    {
        return $this->replies()->count();
    }

    /**
     * Get waktu relatif dari komentar (untuk tampilan)
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
