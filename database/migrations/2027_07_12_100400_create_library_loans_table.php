<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Librarian (Physical) workflow, one row per checkout:
     *
     *   active -> returned (on time or late — a late return spins off
     *             a library_fines row automatically)
     *          -> lost (copy never came back)
     *
     * A loan can be renewed in place (renewal_count + due_at pushed
     * out) up to the circulation policy's max_renewals, as long as no
     * one else is holding the title.
     */
    public function up(): void
    {
        Schema::create('library_loans', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_book_copy_id')
                ->constrained('library_book_copies')
                ->cascadeOnDelete();

            $table->foreignId('library_member_id')
                ->constrained('library_members')
                ->cascadeOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('returned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('checked_out_at');
            $table->timestamp('due_at');
            $table->timestamp('returned_at')->nullable();

            $table->unsignedTinyInteger('renewal_count')->default(0);

            $table->string('status')->default('active');
            // active, returned, lost

            $table->timestamps();

            $table->index('status');
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_loans');
    }
};
