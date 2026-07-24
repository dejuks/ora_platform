<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The core entity of Repository Management: a scholarly deposit
     * that carries full Dublin Core bibliographic metadata and moves
     * through:
     *
     *   submitted -> metadata_review -> content_review ->
     *   (revision_requested | recommended) -> (rejected | approved) ->
     *   published
     *
     * Bibliography is embedded throughout — not bolted on at the end —
     * so the Dublin Core fields (creator, subject, publisher, date,
     * identifier, source, language, relation, coverage, rights) live
     * directly on this table alongside the workflow state.
     */
    public function up(): void
    {
        Schema::create('repository_items', function (Blueprint $table) {

            $table->id();

            /*
            |----------------------------------------------------------------
            | Dublin Core bibliographic metadata
            |----------------------------------------------------------------
            */

            $table->string('title'); // dc:title
            $table->string('authors'); // dc:creator — citation-ready author list
            $table->text('abstract'); // dc:description

            $table->string('resource_type')->default('article'); // dc:type
            $table->string('keywords')->nullable(); // dc:subject
            $table->string('publisher')->nullable(); // dc:publisher
            $table->string('contributors')->nullable(); // dc:contributor
            $table->date('publication_date')->nullable(); // dc:date
            $table->string('source')->nullable(); // dc:source (journal/conference/series)
            $table->string('language', 10)->default('en'); // dc:language
            $table->string('external_identifier')->nullable(); // dc:identifier (existing DOI/ISBN, if any)
            $table->string('related_identifiers')->nullable(); // dc:relation
            $table->string('coverage')->nullable(); // dc:coverage (spatial/temporal scope)
            $table->text('rights_statement')->nullable(); // dc:rights — copyright/licence statement
            $table->text('bibliographic_references')->nullable(); // reference list / bibliography cited

            /*
            |----------------------------------------------------------------
            | File & access
            |----------------------------------------------------------------
            */

            $table->string('file_path')->nullable();

            $table->string('access_level')->default('restricted');
            // open, restricted

            $table->date('embargo_until')->nullable();

            /*
            |----------------------------------------------------------------
            | Workflow
            |----------------------------------------------------------------
            */

            $table->string('status')->default('submitted');
            // submitted, metadata_review, content_review,
            // revision_requested, recommended, rejected, approved, published

            $table->foreignId('depositor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Repository Curator — metadata validation & enrichment
            $table->foreignId('curator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('curator_notes')->nullable();
            $table->string('controlled_vocabulary')->nullable(); // curator-assigned subject tags
            $table->boolean('copyright_verified')->default(false);
            $table->timestamp('curated_at')->nullable();

            // Content Reviewer — academic & citation integrity review
            $table->foreignId('content_reviewer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reviewer_recommendation')->nullable();
            // approve, minor_revision, major_revision, reject

            $table->text('reviewer_notes')->nullable();
            $table->boolean('plagiarism_checked')->default(false);
            $table->timestamp('reviewed_at')->nullable();

            // Repository Administrator — final decision & publication
            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('decision_notes')->nullable();
            $table->timestamp('decided_at')->nullable();

            $table->string('persistent_url')->nullable()->unique();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('downloads_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('access_level');
            $table->index('resource_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repository_items');
    }
};
