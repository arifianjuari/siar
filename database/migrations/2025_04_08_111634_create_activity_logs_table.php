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
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action')->nullable();
                $table->nullableMorphs('model');
                $table->text('changes')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('action');
                $table->index('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak menghapus tabel yang sudah ada sebelumnya
    }
};
