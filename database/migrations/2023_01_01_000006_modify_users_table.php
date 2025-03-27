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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom tenant_id setelah id
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');

            // Tambahkan kolom role_id setelah tenant_id
            $table->foreignId('role_id')->after('tenant_id')->constrained()->onDelete('cascade');

            // Tambahkan kolom is_active setelah role_id
            $table->boolean('is_active')->after('role_id')->default(true);

            // Tambahkan kolom last_login_at setelah is_active
            $table->timestamp('last_login_at')->after('is_active')->nullable();

            // Tambahkan kolom last_login_ip setelah last_login_at
            $table->string('last_login_ip')->after('last_login_at')->nullable();

            // Tambahkan kolom last_login_user_agent setelah last_login_ip
            $table->string('last_login_user_agent')->after('last_login_ip')->nullable();

            // Tambahkan kolom created_by setelah last_login_user_agent
            $table->foreignId('created_by')->after('last_login_user_agent')->nullable()->constrained('users')->onDelete('set null');

            // Tambahkan kolom updated_by setelah created_by
            $table->foreignId('updated_by')->after('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Tambahkan kolom deleted_at setelah updated_by
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'tenant_id',
                'role_id',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'last_login_user_agent',
                'created_by',
                'updated_by',
                'deleted_at'
            ]);
        });
    }
};
