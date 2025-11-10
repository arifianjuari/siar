<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'parent_id',
        'order'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    /**
     * Boot function untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Otomatis generate slug dari name ketika membuat atau mengupdate
        static::saving(function ($tag) {
            if (empty($tag->slug) || $tag->isDirty('name')) {
                $tag->slug = Str::slug($tag->name);

                // Pastikan slug unik dalam tenant yang sama
                $count = 1;
                $originalSlug = $tag->slug;

                while (static::where('tenant_id', $tag->tenant_id)
                    ->where('slug', $tag->slug)
                    ->where('id', '!=', $tag->id ?: 0)
                    ->exists()
                ) {
                    $tag->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    /**
     * Get the tenant that owns the tag.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the parent tag.
     */
    public function parent()
    {
        return $this->belongsTo(Tag::class, 'parent_id');
    }

    /**
     * Get the child tags.
     */
    public function children()
    {
        return $this->hasMany(Tag::class, 'parent_id')->orderBy('order');
    }

    /**
     * Morph relasi ke semua jenis dokumen
     */
    public function documents()
    {
        return $this->morphedByMany(Model::class, 'document', 'document_tag', 'tag_id', 'document_id')
            ->select('document_tag.document_type', 'document_tag.document_id');
    }

    /**
     * Morph relasi khusus untuk RiskReport
     */
    public function riskReports()
    {
        return $this->morphedByMany(RiskReport::class, 'document', 'document_tag', 'tag_id', 'document_id')
            ->select('risk_reports.*')
            ->where('risk_reports.tenant_id', $this->tenant_id);
    }

    /**
     * Morph relasi khusus untuk Correspondence
     */
    public function correspondences()
    {
        return $this->morphedByMany(Correspondence::class, 'document', 'document_tag', 'tag_id', 'document_id')
            ->select('correspondences.*')
            ->where('correspondences.tenant_id', $this->tenant_id);
    }

    /**
     * Morph relasi khusus untuk SPO
     */
    public function spos()
    {
        return $this->morphedByMany(SPO::class, 'document', 'document_tag', 'tag_id', 'document_id')
            ->select('spos.*')
            ->where('spos.tenant_id', $this->tenant_id);
    }

    /**
     * Scope query untuk tenant berdasarkan user yang login
     */
    public function scopeTenant($query)
    {
        return $query->where('tags.tenant_id', auth()->user()->tenant_id ?? null);
    }

    /**
     * Scope a query to only include tags belonging to a given tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tags.tenant_id', $tenantId);
    }
}
