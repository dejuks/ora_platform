<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The community discussion thread on an Articles for Deletion
     * nomination — any logged-in member of the module can weigh in
     * with a keep/delete stance (or just a comment) before a Sysop
     * closes it.
     */
    public function up(): void
    {
        Schema::create('article_deletion_comments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('discussion_id')
                ->constrained('article_deletion_discussions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('stance')->default('comment');
            // keep, delete, comment

            $table->text('comment');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_deletion_comments');
    }
};
