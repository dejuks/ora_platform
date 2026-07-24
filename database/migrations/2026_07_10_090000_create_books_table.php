<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A book moves through the ORA eBook Publishing workflow:
     *
     *   submitted -> screening -> desk_rejected, or
     *   submitted -> screening -> under_review -> minor_revision/major_revision ->
     *   accepted -> financial_clearance -> in_production -> published
     *   (or rejected at the editorial decision step)
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->text('abstract');
            $table->string('keywords')->nullable();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')->default('submitted');
            // submitted, screening, desk_rejected, under_review,
            // minor_revision, major_revision, accepted, rejected,
            // financial_clearance, in_production, published

            $table->string('manuscript_file')->nullable();
            // author's original submitted file

            $table->text('editor_decision_notes')->nullable();
            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------
            | Financial clearance — Book Processing Charge (BPC)
            |--------------------------------------------------------
            */
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            // unpaid, pending, paid, waived
            $table->timestamp('fee_paid_at')->nullable();
            $table->boolean('waiver_requested')->default(false);
            $table->text('waiver_reason')->nullable();
            $table->foreignId('cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cleared_at')->nullable();

            /*
            |--------------------------------------------------------
            | Digital production — Digital Content Manager
            |--------------------------------------------------------
            */
            $table->string('isbn')->nullable();
            $table->string('doi')->nullable();
            $table->string('ebook_pdf')->nullable();
            $table->string('ebook_epub')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('access_type')->default('open_access');
            // open_access, restricted, embargoed
            $table->timestamp('embargo_until')->nullable();
            $table->foreignId('produced_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->unsignedInteger('downloads_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('access_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
