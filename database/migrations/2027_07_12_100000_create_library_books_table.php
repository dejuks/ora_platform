<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A library book is the bibliographic record (the "title"), not a
     * physical item — the physical items are tracked one-per-copy in
     * library_book_copies below. It moves through a light acquisition
     * gate before it enters circulation:
     *
     *   pending_acquisition -> active (approved by the Library Manager)
     *                       -> withdrawn (removed from the collection)
     */
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable()->index();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('edition')->nullable();

            // Cataloger: DDC/LCC classification and call number.
            $table->string('call_number')->nullable();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();

            $table->string('status')->default('pending_acquisition');
            // pending_acquisition, active, withdrawn

            $table->foreignId('cataloged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
