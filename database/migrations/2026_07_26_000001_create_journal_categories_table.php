<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The fixed list of subject categories (Fiction, Literature,
     * Science, ...) that a manuscript is tagged with, mirroring the
     * same pattern as wiki_categories for the Wiki module.
     */
    public function up(): void
    {
        Schema::create('journal_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('manuscripts', function (Blueprint $table) {
            if (!Schema::hasColumn('manuscripts', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('author_id')
                    ->constrained('journal_categories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            if (Schema::hasColumn('manuscripts', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::dropIfExists('journal_categories');
    }
};
