<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentReference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reference_type',
        'reference_number',
        'title',
        'issued_by',
        'issued_date',
        'related_unit',
        'file_url',
        'description',
        'tags',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'tags' => 'array',
    ];

    public static $referenceTypes = [
        'Peraturan Perundangan' => 'Peraturan Perundangan',
        'Peraturan Kapolri' => 'Peraturan Kapolri',
        'Surat Keputusan' => 'Surat Keputusan',
        'Surat Eksternal' => 'Surat Eksternal',
        'Surat Internal' => 'Surat Internal',
        'Pedoman' => 'Pedoman',
        'SOP' => 'Standar Operasional Prosedur',
        'Dokumen Lainnya' => 'Dokumen Lainnya'
    ];
}
