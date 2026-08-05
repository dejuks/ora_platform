<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The final gate before publish(): once the Article Processing
     * Charge is settled, the Journal Manager sends the fully-typeset
     * publication proof (the actual document that will go live) to
     * the corresponding author. The author reads it and either
     * approves it as-is, or sends comments back and the Manager
     * revises and re-sends. Only an 'approved' proof lets the
     * Manager's publish() button fire — this is independent of
     * manuscript.status, the same way payment_status is, so it can
     * be re-sent/re-reviewed without disturbing the 'accepted' status.
     */
    public function up(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {

            $table->string('proof_file')->nullable()->after('doi');

            $table->string('proof_status')->default('not_sent')->after('proof_file');
            // not_sent, sent, approved, changes_requested

            $table->text('proof_message')->nullable()->after('proof_status');
            // Manager's note to the author when the proof was sent.

            $table->text('proof_feedback')->nullable()->after('proof_message');
            // Author's comments, when they request changes instead of approving.

            $table->foreignId('proof_sent_by')
                ->nullable()
                ->after('proof_feedback')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('proof_sent_at')->nullable()->after('proof_sent_by');
            $table->timestamp('proof_responded_at')->nullable()->after('proof_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proof_sent_by');
            $table->dropColumn([
                'proof_file',
                'proof_status',
                'proof_message',
                'proof_feedback',
                'proof_sent_at',
                'proof_responded_at',
            ]);
        });
    }
};
