<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Document extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'document_number',
        'document_title',
        'document_date',
        'category',
        'description',
        'confidentiality_level',
        'document_type',
        'document_scope',
        'is_regulation',
        'revision_number',
        'revision_date',
        'superseded_by_id',
        'storage_location',
        'distribution_note',
        'last_evaluated_at',
        'evaluated_by',
        'is_active',
        'file_path',
        'uploaded_by'
    ];

    protected $casts = [
        'document_date' => 'datetime',
        'revision_date' => 'date',
        'last_evaluated_at' => 'datetime',
        'is_regulation' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that uploaded the document.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user that evaluated the document.
     */
    public function evaluatedBy()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Get the document that superseded this document.
     */
    public function supersededBy()
    {
        return $this->belongsTo(Document::class, 'superseded_by_id');
    }

    /**
     * Get all documents that were superseded by this document.
     */
    public function supersedes()
    {
        return $this->hasMany(Document::class, 'superseded_by_id');
    }

    /**
     * Get the tenant that owns the document.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the tags for the document.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'document', 'document_tag', 'document_id', 'tag_id')
            ->where('tags.tenant_id', $this->tenant_id)
            ->orderBy('tags.name');
    }

    /**
     * Get the documentables for this document.
     */
    public function documentables()
    {
        return $this->hasMany(Documentable::class);
    }

    /**
     * Get risk reports related to this document
     */
    public function riskReports()
    {
        return $this->morphedByMany(\App\Models\RiskReport::class, 'documentable', 'documentables');
    }

    /**
     * Scope a query to only include documents for the current tenant.
     */
    public function scopeForTenant($query)
    {
        return $query->where('tenant_id', auth()->user()->tenant_id);
    }

    /**
     * Scope a query to only include regulation documents.
     */
    public function scopeRegulations($query)
    {
        return $query->where('is_regulation', true);
    }

    /**
     * Scope a query to filter by document type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope a query to filter by document scope.
     */
    public function scopeOfScope($query, $scope)
    {
        return $query->where('document_scope', $scope);
    }

    /**
     * Scope a query to only include active documents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive (superseded) documents.
     */
    public function scopeSuperseded($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Attach a tag by its slug
     *
     * @param string $slug
     * @return bool
     */
    public function attachTagBySlug($slug)
    {
        $tenantId = $this->tenant_id;

        try {
            $tag = Tag::where('tenant_id', $tenantId)
                ->whereRaw('LOWER(slug) = ?', [strtolower($slug)])
                ->firstOrFail();

            if ($this->tags()->where('tags.id', $tag->id)->exists()) {
                return true;
            }

            $this->tags()->attach($tag->id);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error attaching tag to document', [
                'document_id' => $this->id,
                'tag_slug' => $slug,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
