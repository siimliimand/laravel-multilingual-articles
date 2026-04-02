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
        Schema::table('article_translations', function (Blueprint $table) {
            $table->index(['language_code', 'status'], 'article_translations_language_status_index');
            $table->index('path', 'article_translations_path_index');
            $table->index('updated_at', 'article_translations_updated_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_translations', function (Blueprint $table) {
            $table->dropIndex('article_translations_language_status_index');
            $table->dropIndex('article_translations_path_index');
            $table->dropIndex('article_translations_updated_at_index');
        });
    }
};
