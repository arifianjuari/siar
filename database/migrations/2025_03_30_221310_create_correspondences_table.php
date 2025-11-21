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
        Schema::create('correspondences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('document_number');
            $table->string('document_title');
            $table->enum('document_type', ['Regulasi', 'Bukti'])->default('Bukti');
            $table->string('document_version');
            $table->date('document_date');
            $table->enum('confidentiality_level', ['Internal', 'Publik', 'Rahasia']);
            $table->string('file_path');
            $table->timestamp('next_review')->nullable();
            $table->string('origin_module')->nullable();
            $table->bigInteger('origin_record_id')->nullable();
            $table->string('subject');
            $table->longText('body');
            $table->text('reference_to')->nullable();
            $table->string('sender_name');
            $table->string('sender_position');
            $table->string('recipient_name');
            $table->string('recipient_position');
            $table->text('cc_list')->nullable();
            $table->string('signed_at_location');
            $table->date('signed_at_date');
            $table->string('signatory_name');
            $table->string('signatory_position');
            $table->string('signatory_rank')->nullable();
            $table->string('signatory_nrp')->nullable();
            $table->string('signature_file')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};
