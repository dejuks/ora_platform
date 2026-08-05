<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Co-authors are NOT users of the platform — most journals accept
     * co-authors who never create an account here, so this is a
     * plain child table of free-text fields keyed to the manuscript,
     * not a pivot to `users`. `author_id` on manuscripts stays the
     * one corresponding/submitting author (who owns edit rights,
     * pays the APC, approves the proof, etc.); everyone in this
     * table is additional byline credit only.
     */
    public function up(): void
    {
        Schema::create('manuscript_co_authors', function (Blueprint $table) {

            $table->id();

            $table->foreignId('manuscript_id')
                ->constrained('manuscripts')
                ->cascadeOnDelete();

            $table->string('full_name');

            // Everything below is genuinely optional — many journals
            // only ever collect the name at submission time and fill
            // in the rest during production.
            $table->string('email')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('orcid')->nullable();

            // Display order on the byline; also lets the submitting
            // author reorder without the co-author rows silently
            // reshuffling themselves.
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();

            $table->index(['manuscript_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manuscript_co_authors');
    }
};
