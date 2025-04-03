<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi terkonsolidasi untuk modul manajemen dokumen.
     */
    public function up(): void
    {
        // Tabel Tags (Tag/Kategori Dokumen)
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Unique constraint untuk slug di tenant yang sama
            $table->unique(['tenant_id', 'slug']);
        });

        // Tabel Documents (Dokumen)
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('version')->default('1.0');
            $table->text('description')->nullable();
            $table->enum('confidentiality_level', ['Internal', 'Publik', 'Rahasia'])->default('Internal');
            $table->date('published_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->date('review_date')->nullable();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);

            // Kolom khusus untuk dokumen KARS
            $table->string('document_number')->nullable();
            $table->string('standard_number')->nullable();
            $table->string('element_number')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_category')->nullable();
            $table->string('document_group')->nullable();

            $table->timestamps();
        });

        // Tabel Document_Tag (Relasi Many-to-Many antara Document dan Tag)
        Schema::create('document_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Unique constraint
            $table->unique(['document_id', 'tag_id']);
        });

        // Tabel Documentables (Relasi Polimorfik antara Document dan Entity lain)
        Schema::create('documentables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->morphs('documentable');
            $table->timestamps();

            // Unique constraint
            $table->unique(['document_id', 'documentable_id', 'documentable_type'], 'unique_document_relation');
        });

        // Tabel Document_References (Referensi antar Dokumen)
        Schema::create('document_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('target_document_id')->constrained('documents')->onDelete('cascade');
            $table->enum('reference_type', [
                'mengacu',               // Referensi umum
                'melaksanakan',          // Dokumen pelaksanaan
                'menindaklanjuti',       // Dokumen tindak lanjut
                'melengkapi',            // Dokumen pelengkap
                'menjadi_dasar',         // Dokumen dasar
                'sebagai_lampiran',      // Dokumen lampiran
                'sebagai_bukti',         // Dokumen bukti
                'menggantikan',          // Dokumen pengganti
                'membatalkan',           // Dokumen pembatalan
                'mengubah',              // Dokumen perubahan
                'memperjelas'            // Dokumen penjelas
            ])->default('mengacu');
            $table->text('description')->nullable();
            $table->timestamps();

            // Unique constraint
            $table->unique(['source_document_id', 'target_document_id', 'reference_type'], 'unique_document_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_references');
        Schema::dropIfExists('documentables');
        Schema::dropIfExists('document_tag');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('tags');
    }
};
