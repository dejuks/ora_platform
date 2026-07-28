<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The fixed list of subject categories a catalog title is tagged
     * with, mirroring the same pattern as journal_categories for the
     * Journal module. Deliberately separate from the free-text
     * `subject` column already on library_books — that field stays as
     * a librarian's own cataloging note, while category_id is the
     * structured value the public portal filters/browses by.
     */
    public function up(): void
    {
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('library_books', function (Blueprint $table) {
            if (!Schema::hasColumn('library_books', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('subject')
                    ->constrained('library_categories')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('library_books', function (Blueprint $table) {
            if (Schema::hasColumn('library_books', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::dropIfExists('library_categories');
    }
};
