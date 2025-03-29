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
        'title',
        'file_path',
        'document_number',
        'document_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'document_date' => 'date',
    ];

    /**
     * Get the user that created the document.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
}
