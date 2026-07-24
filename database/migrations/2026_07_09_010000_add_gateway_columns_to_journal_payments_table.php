<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original journal_payments migration was edited in place to
     * add Chapa-specific columns after some environments had already
     * run it. This migration brings those environments up to date
     * without touching the ones that migrated fresh (hasColumn guards
     * make it a safe no-op there).
     */
    public function up(): void
    {
        Schema::table('journal_payments', function (Blueprint $table) {

            if (! Schema::hasColumn('journal_payments', 'gateway')) {
                $table->string('gateway')->default('chapa')->after('currency');
            }

            if (! Schema::hasColumn('journal_payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable()->after('notes');
            }
        });

        // transaction_ref needs to be unique for Chapa's tx_ref lookups.
        // Guard against re-adding it if this migration runs twice.
        if (! $this->hasUniqueIndex('journal_payments', 'transaction_ref')) {
            Schema::table('journal_payments', function (Blueprint $table) {
                $table->unique('transaction_ref');
            });
        }
    }

    public function down(): void
    {
        Schema::table('journal_payments', function (Blueprint $table) {
            if (Schema::hasColumn('journal_payments', 'gateway')) {
                $table->dropColumn('gateway');
            }

            if (Schema::hasColumn('journal_payments', 'gateway_response')) {
                $table->dropColumn('gateway_response');
            }
        });
    }

    protected function hasUniqueIndex(string $table, string $column): bool
    {
        $indexes = Schema::getIndexes($table);

        foreach ($indexes as $index) {
            if ($index['unique'] && in_array($column, $index['columns'], true)) {
                return true;
            }
        }

        return false;
    }
};
