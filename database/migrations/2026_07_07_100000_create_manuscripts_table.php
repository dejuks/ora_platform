<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The core entity of Journal Management: a manuscript moves
     * through submitted -> screening -> under_review ->
     * revision_requested -> accepted/rejected -> published.
     */
    public function up(): void
    {
        Schema::create('manuscripts', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->text('abstract');
            $table->string('keywords')->nullable();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('associate_editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')->default('submitted');
            // submitted, screening, desk_rejected, under_review,
            // revision_requested, accepted, rejected, published

            $table->string('manuscript_file')->nullable();

            $table->text('editor_decision_notes')->nullable();
            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('doi')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscripts');
    }
};
