<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per payment attempt against a manuscript's publication
     * fee. Kept separate from manuscripts so the author's full payment
     * history (retries, receipts) is preserved even though the
     * manuscript only ever cares about its latest status.
     */
    public function up(): void
    {
        Schema::create('journal_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('manuscript_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('ETB');

            $table->string('gateway')->default('chapa');

            $table->string('method')->default('chapa');
            // chapa, manual, waiver

            $table->string('status')->default('pending');
            // pending, completed, failed, waived

            $table->string('transaction_ref')->nullable()->unique();
            // our own tx_ref, sent to Chapa and used to verify() later

            $table->text('notes')->nullable();
            $table->json('gateway_response')->nullable();
            // raw payload from Chapa's verify() call, kept for audit trail

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_payments');
    }
};
