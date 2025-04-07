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
        if (!Schema::hasTable('spos')) {
            Schema::create('spos', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('work_unit_id')->constrained()->onDelete('restrict');
                $table->string('document_title');
                $table->enum('document_type', ['Kebijakan', 'Pedoman', 'SPO', 'Perencanaan', 'Program'])->default('SPO');
                $table->string('document_number');
                $table->date('document_date');
                $table->string('document_version');
                $table->enum('confidentiality_level', ['Internal', 'Publik', 'Rahasia'])->default('Publik');
                $table->string('file_path');
                $table->timestamp('next_review')->nullable();
                $table->integer('review_cycle_months')->default(12);
                $table->enum('status_validasi', ['Draft', 'Disetujui', 'Kadaluarsa', 'Revisi'])->default('Draft');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('definition')->nullable();
                $table->text('purpose')->nullable();
                $table->text('policy')->nullable();
                $table->longText('procedure')->nullable();
                $table->json('linked_unit')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spos');
    }
};
