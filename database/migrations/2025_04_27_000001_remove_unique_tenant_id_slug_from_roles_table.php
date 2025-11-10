<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah index ada
        $indexExists = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_tenant_id_slug_unique'");
        
        if (!empty($indexExists)) {
            try {
                // Coba hapus menggunakan Schema facade (lebih aman)
                Schema::table('roles', function (Blueprint $table) {
                    $table->dropUnique(['tenant_id', 'slug']);
                });
            } catch (\Exception $e) {
                // Jika gagal karena foreign key constraint, coba pendekatan alternatif
                // Hapus foreign key constraint sementara, hapus index, lalu tambahkan kembali foreign key
                try {
                    // Cek apakah ada foreign key pada tenant_id
                    $fkExists = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'roles' 
                        AND COLUMN_NAME = 'tenant_id'
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                        LIMIT 1
                    ");
                    
                    if (!empty($fkExists)) {
                        $fkName = $fkExists[0]->CONSTRAINT_NAME;
                        
                        // Hapus foreign key sementara
                        DB::statement("ALTER TABLE roles DROP FOREIGN KEY `{$fkName}`");
                        
                        // Hapus index
                        DB::statement('ALTER TABLE roles DROP INDEX roles_tenant_id_slug_unique;');
                        
                        // Tambahkan kembali foreign key
                        DB::statement("
                            ALTER TABLE roles 
                            ADD CONSTRAINT `{$fkName}` 
                            FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
                        ");
                    } else {
                        // Jika tidak ada foreign key, langsung hapus index
                        DB::statement('ALTER TABLE roles DROP INDEX roles_tenant_id_slug_unique;');
                    }
                } catch (\Exception $e2) {
                    // Jika masih gagal, skip migration ini
                    // Unique constraint masih bisa di-enforce di application level
                    // Log error untuk debugging
                    \Log::warning('Failed to drop unique index roles_tenant_id_slug_unique: ' . $e2->getMessage());
                }
            }
        }
    }

    public function down(): void
    {
        // Cek apakah index sudah ada
        $indexExists = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_tenant_id_slug_unique'");
        
        if (empty($indexExists)) {
            // Tambahkan kembali unique index
            Schema::table('roles', function (Blueprint $table) {
                $table->unique(['tenant_id', 'slug'], 'roles_tenant_id_slug_unique');
            });
        }
    }
};
