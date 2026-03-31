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
        Schema::create('article_translations', function (Blueprint $table) {
            $table->bigIncrements('article_translation_id');
            $table->unsignedBigInteger('article_id');
            $table->string('language_code', 2);
            $table->string('title', 70);
            $table->string('path', 70);
            $table->string('summary', 180)->nullable();
            $table->string('keywords', 255)->nullable();
            $table->longText('content');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('modified_by')->nullable();
            $table->enum('status', ['draft', 'published', 'unpublished'])->default('draft');
            $table->timestamp('unpublished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('article_id')
                ->references('article_id')
                ->on('articles')
                ->onDelete('cascade');

            $table->foreign('language_code')
                ->references('language_code')
                ->on('site_languages')
                ->onDelete('restrict');

            $table->unique(['path', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_translations');
    }
};
