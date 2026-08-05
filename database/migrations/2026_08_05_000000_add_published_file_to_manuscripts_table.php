<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The version-of-record is deliberately its own file, distinct
     * from both manuscript_file (the blind peer-review copy) and
     * proof_file (the pre-DOI document the author approved):
     *
     *   manuscript_file -> what reviewers read (should carry no
     *                      author-identifying info; blinding is
     *                      enforced by never showing author metadata
     *                      to reviewers in the UI, not by editing
     *                      this file).
     *   proof_file      -> what the author approved: the typeset
     *                      document with author details composed
     *                      back in, before a DOI exists.
     *   published_file  -> the version-of-record: the same approved
     *                      content, now carrying the DOI/citation
     *                      footer. Defaults to proof_file at publish
     *                      time if the Manager doesn't attach a
     *                      separately DOI-stamped file.
     */
    public function up(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->string('published_file')->nullable()->after('proof_responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropColumn('published_file');
        });
    }
};
