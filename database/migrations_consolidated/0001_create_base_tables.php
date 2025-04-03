<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi terkonsolidasi untuk tabel-tabel dasar.
     */
    public function up(): void
    {
        // Tabel Tenants (Pelanggan/Institusi)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->text('letter_head')->nullable();
            $table->string('domain')->unique();
            $table->string('database')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Work_Units (Unit Kerja)
        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('unit_code');
            $table->string('unit_name');
            $table->text('description')->nullable();
            $table->enum('unit_type', ['medical', 'non-medical', 'supporting'])->nullable();
            $table->foreignId('head_of_unit_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('work_units')->nullOnDelete();
            $table->string('level')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Tabel Roles (Peran/Jabatan)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Role name should be unique within a tenant
            $table->unique(['tenant_id', 'slug']);
        });

        // Tabel Users (Pengguna)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position')->nullable();
            $table->string('rank')->nullable();
            $table->string('nrp')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('last_login_user_agent')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('profile_photo')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('work_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('employment_status', ['aktif', 'resign', 'cuti', 'magang'])->default('aktif');
        });

        // Tabel Modules (Modul Aplikasi)
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Tenant_Modules (Modul yang diaktifkan untuk tenant)
        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->enum('request_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->text('request_notes')->nullable();
            $table->timestamps();

            // Unique tenant-module combination
            $table->unique(['tenant_id', 'module_id']);
        });

        // Tabel Tenant_Module_Configs (Konfigurasi khusus modul per tenant)
        Schema::create('tenant_module_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'module_id', 'key']);
        });

        // Tabel Role_Module_Permissions (Hak akses peran pada modul)
        Schema::create('role_module_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->json('permissions');
            $table->timestamps();

            $table->unique(['role_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_module_permissions');
        Schema::dropIfExists('tenant_module_configs');
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('users');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('work_units');
        Schema::dropIfExists('tenants');
    }
};
