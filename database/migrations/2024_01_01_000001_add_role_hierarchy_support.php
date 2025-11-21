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
        // Add parent_role_id to roles table for hierarchy
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_role_id')->nullable()->after('tenant_id');
            $table->integer('level')->default(0)->after('parent_role_id')->comment('Hierarchy level: 0=highest');
            $table->boolean('inherit_permissions')->default(true)->after('level');
            
            $table->foreign('parent_role_id')->references('id')->on('roles')->onDelete('set null');
            $table->index(['tenant_id', 'level']);
        });

        // Create user_permissions table for user-level permission overrides
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('tenant_id');
            $table->boolean('can_view')->default(false);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_import')->default(false);
            $table->boolean('can_export')->default(false);
            $table->enum('type', ['grant', 'revoke'])->default('grant')->comment('Grant adds permission, revoke removes it');
            $table->string('reason')->nullable()->comment('Reason for override');
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('granted_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['user_id', 'module_id']);
            $table->index(['tenant_id', 'user_id']);
            $table->index('expires_at');
        });

        // Add audit trail for permission changes
        Schema::create('permission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('entity_type')->comment('role or user');
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('module_id');
            $table->json('old_permissions')->nullable();
            $table->json('new_permissions');
            $table->string('action')->comment('create, update, delete');
            $table->unsignedBigInteger('changed_by');
            $table->string('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['tenant_id', 'entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_audit_logs');
        Schema::dropIfExists('user_permissions');
        
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent_role_id']);
            $table->dropColumn(['parent_role_id', 'level', 'inherit_permissions']);
        });
    }
};
