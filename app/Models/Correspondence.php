<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Correspondence extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'document_number',
        'document_title',
        'document_type',
        'document_version',
        'document_date',
        'confidentiality_level',
        'file_path',
        'next_review',
        'origin_module',
        'origin_record_id',
        'subject',
        'body',
        'reference_to',
        'sender_name',
        'sender_position',
        'recipient_name',
        'recipient_position',
        'cc_list',
        'signed_at_location',
        'signed_at_date',
        'signatory_name',
        'signatory_position',
        'signatory_rank',
        'signatory_nrp',
        'signature_file',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'document_date' => 'date',
        'next_review' => 'datetime',
        'signed_at_date' => 'date',
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array
     */
    protected $with = ['tags'];

    /**
     * Get the creator of the correspondence.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the tags for the correspondence.
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
     * Get the documents for the correspondence.
     */
    public function documents()
    {
        return $this->morphToMany(Document::class, 'documentable', 'documentables')
            ->where('documents.tenant_id', $this->tenant_id)
            ->withPivot('relation_type')
            ->withTimestamps();
    }
}
