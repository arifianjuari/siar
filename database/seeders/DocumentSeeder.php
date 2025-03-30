<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Tag;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat dokumen contoh
        $doc = Document::create([
            'tenant_id' => 1,
            'document_number' => 'DOC/RS/2025/001',
            'document_title' => 'Laporan Risiko Unit Rawat Jalan',
            'document_date' => now(),
            'category' => 'risiko',
            'description' => 'Laporan risiko untuk unit rawat jalan',
            'confidentiality_level' => 'internal',
            'uploaded_by' => 1
        ]);

        // Membuat tag
        $tag = Tag::firstOrCreate([
            'tenant_id' => 1,
            'slug' => 'pmkp-3-1',
        ], [
            'name' => 'PMKP 3.1',
        ]);

        // Melampirkan tag ke dokumen
        $doc->tags()->attach($tag->id);

        // Membuat contoh dokumen lain
        $doc2 = Document::create([
            'tenant_id' => 1,
            'document_number' => 'DOC/RS/2025/002',
            'document_title' => 'Prosedur Audit Internal',
            'document_date' => now()->subDays(7),
            'category' => 'audit',
            'description' => 'Prosedur untuk melakukan audit internal',
            'confidentiality_level' => 'confidential',
            'uploaded_by' => 1
        ]);

        // Membuat tag lain
        $tag2 = Tag::firstOrCreate([
            'tenant_id' => 1,
            'slug' => 'rsk-1-2',
        ], [
            'name' => 'RSK 1.2',
        ]);

        // Melampirkan tag ke dokumen
        $doc2->tags()->attach($tag2->id);
    }
}
