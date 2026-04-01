<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert node_type and visibility columns from ENUM to VARCHAR(50).
     */
    public function up(): void
    {
        // Convert node_type from ENUM to VARCHAR(50)
        DB::statement("ALTER TABLE articles MODIFY COLUMN node_type VARCHAR(50) NOT NULL");
        
        // Convert visibility from ENUM to VARCHAR(50)
        DB::statement("ALTER TABLE articles MODIFY COLUMN visibility VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     * Revert node_type and visibility columns back to ENUM.
     */
    public function down(): void
    {
        // Revert node_type to ENUM
        DB::statement("ALTER TABLE articles MODIFY COLUMN node_type ENUM('article', 'user_agreement') NOT NULL");
        
        // Revert visibility to ENUM
        DB::statement("ALTER TABLE articles MODIFY COLUMN visibility ENUM('public', 'private') NOT NULL");
    }
};
