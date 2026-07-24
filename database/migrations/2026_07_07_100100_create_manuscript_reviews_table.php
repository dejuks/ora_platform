<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per reviewer assigned to a manuscript. Blinded review:
     * the reviewer only ever sees the manuscript, never the author's
     * identity, enforced at the application layer.
     */
    public function up(): void
    {
        Schema::create('manuscript_reviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('manuscript_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reviewer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status')->default('assigned');
            // assigned, in_progress, submitted, declined

            $table->string('recommendation')->nullable();
            // accept, minor_revision, major_revision, reject

            $table->text('comments_to_author')->nullable();
            $table->text('comments_to_editor')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->unique(['manuscript_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_reviews');
    }
};
