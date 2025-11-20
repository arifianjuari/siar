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
        Schema::table('actionable_items', function (Blueprint $table) {
            // Add UUID for unique identification
            $table->uuid('uuid')->unique()->after('id');
            
            // Add title and description
            $table->string('title')->after('actionable_id');
            $table->text('description')->nullable()->after('title');
            
            // Add due date
            $table->date('due_date')->nullable()->after('description');
            
            // Add priority
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('due_date');
            
            // Add order column for sorting
            $table->integer('order')->default(0)->after('priority');
            
            // Add completed tracking
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->foreignId('completed_by')->nullable()->constrained('users')->after('completed_at');
            
            // Add updated_by tracking
            $table->foreignId('updated_by')->nullable()->constrained('users')->after('completed_by');
            
            // Add index for order column
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actionable_items', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'title',
                'description',
                'due_date',
                'priority',
                'order',
                'completed_at',
                'completed_by',
                'updated_by',
            ]);
        });
    }
};
