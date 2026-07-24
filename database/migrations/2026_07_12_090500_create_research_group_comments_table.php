<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A reply to a research group discussion post.
     */
    public function up(): void
    {
        Schema::create('research_group_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('research_group_post_id')->constrained('research_group_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('body');
            $table->string('status')->default('published'); // published, removed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_group_comments');
    }
};
