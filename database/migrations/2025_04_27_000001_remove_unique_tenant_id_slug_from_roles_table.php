<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexExists = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_tenant_id_slug_unique'");
        
        if (!empty($indexExists)) {
            DB::statement('ALTER TABLE roles DROP INDEX roles_tenant_id_slug_unique;');
        }
    }

    public function down(): void
    {
        $indexExists = DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_tenant_id_slug_unique'");
        
        if (empty($indexExists)) {
            DB::statement('ALTER TABLE roles ADD UNIQUE INDEX roles_tenant_id_slug_unique (tenant_id, slug);');
        }
    }
};
