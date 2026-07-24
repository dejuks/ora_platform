<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets the Author explain what they changed when resubmitting a
     * manuscript the Book Editor sent back for revision.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('editor_decision_notes');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('revision_notes');
        });
    }
};
