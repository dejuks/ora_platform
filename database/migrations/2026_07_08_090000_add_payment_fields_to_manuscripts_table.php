<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An accepted manuscript carries an Article Processing Charge (APC)
     * that the author must pay before the Journal Manager / EIC can
     * publish it. These columns let the workflow check "has this
     * manuscript been paid for?" without a join, while the full
     * transaction history lives in journal_payments.
     */
    public function up(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {

            $table->decimal('publication_fee', 10, 2)->default(0)->after('doi');

            $table->string('payment_status')->default('unpaid')->after('publication_fee');
            // unpaid, pending, paid, waived

            $table->timestamp('fee_paid_at')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropColumn(['publication_fee', 'payment_status', 'fee_paid_at']);
        });
    }
};
