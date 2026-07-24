<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Articles for Deletion (AfD): any Registered Editor can nominate
     * an article for deletion and the community discusses it, then an
     * Administrator (Sysop) closes the discussion — keep or delete —
     * once consensus is reached.
     */
    public function up(): void
    {
        Schema::create('article_deletion_discussions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('opened_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('reason');

            $table->string('status')->default('open');
            // open, closed_keep, closed_delete

            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('closing_notes')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_deletion_discussions');
    }
};
