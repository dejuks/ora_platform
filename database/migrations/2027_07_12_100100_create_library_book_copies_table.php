<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per physical item on the shelf. A single library_books
     * title can have many copies, each tracked and circulated
     * independently:
     *
     *   pending_acquisition -> available <-> on_loan
     *                                     -> on_hold (reserved for a member)
     *                                     -> lost / damaged / withdrawn
     *
     * Barcode/RFID tagging and status changes from stocktaking audits
     * are the Inventory Manager's responsibility (manage-inventory).
     */
    public function up(): void
    {
        Schema::create('library_book_copies', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_book_id')
                ->constrained('library_books')
                ->cascadeOnDelete();

            $table->string('barcode')->unique();
            $table->string('shelf_location')->nullable();
            $table->string('condition')->default('good');
            // good, worn, damaged

            $table->string('status')->default('pending_acquisition');
            // pending_acquisition, available, on_loan, on_hold, lost, damaged, withdrawn

            $table->foreignId('tagged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_book_copies');
    }
};
