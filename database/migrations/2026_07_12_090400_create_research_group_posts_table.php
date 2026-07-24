<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A forum-style discussion post inside a research group. The
     * Group Moderator can pin an important post, lock one to stop
     * further replies, or remove one that breaks community guidelines.
     */
    public function up(): void
    {
        Schema::create('research_group_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('research_group_id')->constrained('research_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->text('body');

            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->string('status')->default('published'); // published, removed

            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_group_posts');
    }
};
