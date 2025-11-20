<?php

namespace Modules\SPOManagement\Models;

use App\Models\Tag;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkUnit;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SPO extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'spos';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'tenant_id',
        'work_unit_id',
        'document_title',
        'document_type',
        'document_number',
        'document_date',
        'document_version',
        'confidentiality_level',
        'file_path',
        'next_review',
        'review_cycle_months',
        'status_validasi',
        'approved_by',
        'approved_at',
        'definition',
        'purpose',
        'policy',
        'procedure',
        'reference',
        'linked_unit',
        'created_by'
    ];

    /**
     * Atribut yang harus dikonversi.
     *
     * @var array
     */
    protected $casts = [
        'document_date' => 'date',
        'next_review' => 'datetime',
        'approved_at' => 'datetime',
        'linked_unit' => 'json',
        'review_cycle_months' => 'integer',
    ];

    /**
     * Get the tenant yang memiliki SPO.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the work unit yang memiliki SPO.
     */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * Get the user yang menyetujui SPO.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user yang membuat SPO.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tags for the SPO.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'document', 'document_tag', 'document_id', 'tag_id')
            ->withTimestamps()
            ->orderBy('tags.name');
    }

    /**
     * Attach a tag by its slug
     *
     * @param string $slug
     * @return bool
     */
    public function attachTagBySlug($slug)
    {
        $tag = Tag::where('slug', $slug)
            ->where('tenant_id', $this->tenant_id)
            ->first();

        if ($tag) {
            $this->tags()->syncWithoutDetaching($tag->id);
            return true;
        }

        return false;
    }

    /**
     * Scope untuk SPO aktif (disetujui).
     */
    public function scopeApproved($query)
    {
        return $query->where('status_validasi', 'Disetujui');
    }

    /**
     * Scope untuk SPO draft.
     */
    public function scopeDraft($query)
    {
        return $query->where('status_validasi', 'Draft');
    }

    /**
     * Scope untuk SPO yang perlu direvisi.
     */
    public function scopeNeedsRevision($query)
    {
        return $query->where('status_validasi', 'Revisi');
    }

    /**
     * Scope untuk SPO yang kadaluarsa.
     */
    public function scopeExpired($query)
    {
        return $query->where('status_validasi', 'Kadaluarsa');
    }

    /**
     * Scope untuk SPO berdasarkan tipe dokumen.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope untuk SPO yang perlu di-review dalam periode tertentu.
     */
    public function scopeNeedReviewSoon($query, $days = 30)
    {
        return $query->where('status_validasi', 'Disetujui')
            ->where('next_review', '<=', now()->addDays($days))
            ->orderBy('next_review');
    }

    /**
     * Scope untuk filter berdasarkan work unit.
     */
    public function scopeByWorkUnit($query, $workUnitId)
    {
        return $query->where('work_unit_id', $workUnitId);
    }

    /**
     * Check if SPO is approved.
     */
    public function isApproved(): bool
    {
        return $this->status_validasi === 'Disetujui';
    }

    /**
     * Check if SPO is draft.
     */
    public function isDraft(): bool
    {
        return $this->status_validasi === 'Draft';
    }

    /**
     * Check if SPO is expired.
     */
    public function isExpired(): bool
    {
        return $this->status_validasi === 'Kadaluarsa';
    }

    /**
     * Check if SPO needs revision.
     */
    public function needsRevision(): bool
    {
        return $this->status_validasi === 'Revisi';
    }
}
