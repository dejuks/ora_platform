<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An article can carry more than one category (e.g. an article can
 * be both "History" and "Education"), same as real Wikipedia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_wiki_category', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('wiki_category_id')->constrained('wiki_categories')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['article_id', 'wiki_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_wiki_category');
    }
};
