<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Tipe dan ruang lingkup dokumen
            $table->enum('document_type', ['policy', 'guideline', 'spo', 'program', 'evidence'])->nullable()->after('confidentiality_level');
            $table->enum('document_scope', ['rumahsakit', 'unitkerja'])->nullable()->after('document_type');
            $table->boolean('is_regulation')->default(false)->after('document_scope');

            // Informasi revisi
            $table->string('revision_number')->nullable()->after('is_regulation');
            $table->date('revision_date')->nullable()->after('revision_number');
            $table->unsignedBigInteger('superseded_by_id')->nullable()->after('revision_date');

            // Lokasi penyimpanan dan distribusi
            $table->string('storage_location')->nullable()->after('superseded_by_id');
            $table->text('distribution_note')->nullable()->after('storage_location');

            // Informasi evaluasi
            $table->timestamp('last_evaluated_at')->nullable()->after('distribution_note');
            $table->unsignedBigInteger('evaluated_by')->nullable()->after('last_evaluated_at');

            // Foreign keys
            $table->foreign('superseded_by_id')
                ->references('id')
                ->on('documents')
                ->onDelete('set null');

            $table->foreign('evaluated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Hapus foreign keys terlebih dahulu
            $table->dropForeign(['superseded_by_id']);
            $table->dropForeign(['evaluated_by']);

            // Hapus kolom-kolom
            $table->dropColumn([
                'document_type',
                'document_scope',
                'is_regulation',
                'revision_number',
                'revision_date',
                'superseded_by_id',
                'storage_location',
                'distribution_note',
                'last_evaluated_at',
                'evaluated_by'
            ]);
        });
    }
};
