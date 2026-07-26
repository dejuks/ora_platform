<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editing model, per owner's request: nobody but the article's
 * owner (author) can edit by default. Any other Registered Editor
 * must ask, the owner approves or rejects, and an approval is good
 * for exactly one edit — after that the requester needs to ask
 * again. A Sysop/Bureaucrat (moderate-content) always bypasses this
 * entirely, same as they bypass page protection.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_edit_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();

            $table->text('message')->nullable();

            // pending | approved | rejected
            $table->string('status')->default('pending');

            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();

            // Set the moment the one-time approval is spent on a save.
            // Still counts as "approved" for history, just no longer usable.
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index(['article_id', 'requester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_edit_requests');
    }
};
