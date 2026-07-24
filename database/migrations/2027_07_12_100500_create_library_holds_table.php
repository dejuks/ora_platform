<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A hold is placed against a title (library_books), not a specific
     * copy — whichever copy comes back first fulfills the oldest
     * pending hold in the queue:
     *
     *   pending -> ready (a copy was set aside for this member) ->
     *   fulfilled (checked out to them) -> or cancelled / expired if
     *   they never collect it within hold_expiry_days.
     */
    public function up(): void
    {
        Schema::create('library_holds', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_book_id')
                ->constrained('library_books')
                ->cascadeOnDelete();

            $table->foreignId('library_member_id')
                ->constrained('library_members')
                ->cascadeOnDelete();

            // Set once the hold becomes 'ready', so the copy is
            // reserved and taken out of the general available pool.
            $table->foreignId('library_book_copy_id')
                ->nullable()
                ->constrained('library_book_copies')
                ->nullOnDelete();

            $table->string('status')->default('pending');
            // pending, ready, fulfilled, cancelled, expired

            $table->timestamp('requested_at');
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_holds');
    }
};
