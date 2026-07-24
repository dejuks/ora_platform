<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Digital Librarian's collection: ebooks, journal articles,
     * papers, and other digital files — separate from the physical
     * catalog (library_books) and from the Ebook module's own
     * peer-reviewed publishing pipeline.
     *
     *   draft -> published (metadata verified, access rights set,
     *            file uploaded — "reviewed and approved for quality
     *            and compliance" by the Digital Librarian)
     *         -> archived (pulled from the collection, kept on record)
     *
     * access_level controls who can see/download a published
     * resource, on top of already needing Library module access:
     *   all_users    - anyone with library module access (students,
     *                  researchers, staff alike)
     *   members_only - must additionally hold an active library
     *                  membership (library_members)
     *   staff_only   - restricted to library staff roles
     */
    public function up(): void
    {
        Schema::create('library_digital_resources', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->string('resource_type')->default('ebook');
            // ebook, journal_article, paper, other

            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->string('subject')->nullable();
            $table->string('keywords')->nullable();

            $table->string('access_level')->default('all_users');
            // all_users, members_only, staff_only

            $table->string('status')->default('draft');
            // draft, published, archived

            $table->string('file_path')->nullable();
            $table->string('file_original_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('cover_image')->nullable();

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('access_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_digital_resources');
    }
};
