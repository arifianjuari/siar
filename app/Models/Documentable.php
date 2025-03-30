<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentable extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'documentable_id',
        'documentable_type',
        'relation_type'
    ];

    /**
     * Get the document that owns the documentable.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the documentable model.
     */
    public function documentable()
    {
        return $this->morphTo();
    }
}
