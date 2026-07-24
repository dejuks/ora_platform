<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deliberately a single-row settings table (id = 1) rather than a
     * key/value store — there is exactly one Article Processing
     * Charge configuration in effect at a time, and the Journal
     * Manager (manage-settings) is the only role that can change it.
     * Replaces the old hardcoded config('journal.publication_fee')/
     * config('journal.currency') defaults — those .env values now
     * only seed the first row.
     */
    public function up(): void
    {
        Schema::create('journal_settings', function (Blueprint $table) {

            $table->id();

            $table->decimal('publication_fee', 10, 2)->default(50.00);
            $table->string('currency', 8)->default('ETB');

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_settings');
    }
};
