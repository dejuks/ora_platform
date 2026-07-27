<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Splits what used to be one single "Digital Content Manager
     * converts, assigns ISBN/DOI, sets access, and publishes" action
     * into two steps with an author checkpoint in between, matching
     * the spec's "Author: Approve final proof before publication":
     *
     *   in_production -> (DCM uploads proof)   -> proof_review
     *   proof_review  -> (Author approves)      -> ready_to_publish
     *   proof_review  -> (Author requests changes) -> in_production
     *   ready_to_publish -> (DCM sets access + goes live) -> published
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->timestamp('proof_submitted_at')->nullable()->after('produced_by');
            $table->timestamp('proof_approved_at')->nullable()->after('proof_submitted_at');
            $table->text('proof_change_notes')->nullable()->after('proof_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['proof_submitted_at', 'proof_approved_at', 'proof_change_notes']);
        });
    }
};
