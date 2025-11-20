<?php

namespace Modules\RiskManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\WorkUnit;
use App\Models\Tenant;
use App\Models\Tag;
use App\Models\Document;
use Modules\ActivityManagement\Models\Activity;
use Modules\ActivityManagement\Models\ActionableItem;

class RiskReport extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'work_unit_id',
        'document_number',
        'document_title',
        'chronology',
        'description',
        'immediate_action',
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
        'approved_at',
        'document_date'
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
        'document_date' => 'datetime',
    ];

    /**
     * Status yang tersedia untuk laporan risiko.
     *
     * @var array
     */
    public static $statuses = [
        'Draft' => 'Draft',
        'Ditinjau' => 'Ditinjau',
        'Selesai' => 'Selesai'
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = ['tags'];

    /**
     * Mendapatkan warna badge untuk status.
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Draft' => 'danger',
            'Ditinjau' => 'warning',
            'Selesai' => 'success',
            default => 'secondary'
        };
    }

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

    /**
     * Get the risk analysis associated with the risk report.
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(RiskAnalysis::class, 'risk_report_id');
    }

    /**
     * Get the tags for the risk report.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'document', 'document_tag')
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
        // Pastikan kita mendapatkan tenant ID dari model
        $tenantId = $this->tenant_id;

        try {
            // Cari tag dengan slug dan tenant ID yang sesuai (case insensitive)
            $tag = Tag::where('tenant_id', $tenantId)
                ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
                ->firstOrFail();

            // Pastikan tag belum terpasang sebelumnya
            if ($this->tags()->where('tags.id', $tag->id)->exists()) {
                \Illuminate\Support\Facades\Log::info('Tag sudah terpasang sebelumnya', [
                    'report_id' => $this->id,
                    'tag_id' => $tag->id,
                    'tag_name' => $tag->name
                ]);
                return true; // Anggap sukses karena tag sudah terpasang
            }

            // Debug info
            \Illuminate\Support\Facades\Log::info('Tag ditemukan dengan slug', [
                'report_id' => $this->id,
                'tag_id' => $tag->id,
                'tag_name' => $tag->name,
                'tag_slug' => $tag->slug,
                'tenant_id' => $tenantId
            ]);

            // Jika tag ditemukan, lampirkan ke laporan ini
            $this->tags()->attach($tag->id);

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal melampirkan tag ke laporan risiko', [
                'report_id' => $this->id,
                'tag_slug' => $slug,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Scope a query to only include risk reports with a specific tag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTag($query, $slug)
    {
        return $query->whereHas('tags', function ($q) use ($slug) {
            $q->where('slug', $slug);
        });
    }

    /**
     * Get the documents for the risk report.
     */
    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable', 'documentables')
            ->where('documents.tenant_id', $this->tenant_id)
            ->withPivot('relation_type')
            ->withTimestamps();
    }

    /**
     * Get the work unit for the risk report.
     */
    public function workUnit()
    {
        return $this->belongsTo(WorkUnit::class, 'work_unit_id');
    }

    /**
     * Get the activity associated with this risk report.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Get the actionable items that reference this risk report.
     */
    public function actionableItems()
    {
        return $this->morphMany(ActionableItem::class, 'actionable');
    }
}
