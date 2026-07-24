<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_reviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->foreignId('reviewer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status')->default('assigned');
            // assigned, submitted

            $table->string('recommendation')->nullable();
            // accept, minor_revision, major_revision, reject

            $table->text('comments_to_author')->nullable();
            $table->text('comments_to_editor')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->unique(['book_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_reviews');
    }
};
