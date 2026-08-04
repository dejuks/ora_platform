<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which branch a physical copy actually sits on the shelf at.
     * Nullable rather than NOT NULL so existing copies (cataloged
     * before branches existed) don't need a data-backfill migration
     * to keep working — the app layer requires a branch for any
     * *new* copy going forward (see BookController::storeCopy) and
     * flags legacy copies with no branch in the Inventory Manager's
     * views instead of silently guessing one for them.
     *
     * restrictOnDelete rather than nullOnDelete: a branch holding
     * physical inventory should never be deletable out from under
     * it — copies have to be transferred or withdrawn first.
     */
    public function up(): void
    {
        Schema::table('library_book_copies', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('library_book_id')
                ->constrained('library_branches')
                ->restrictOnDelete();

            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('library_book_copies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
