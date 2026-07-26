<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reverses the earlier grandfather-in migration
     * (2027_07_26_000100_backfill_email_verified_at_for_existing_users).
     * That migration marked every existing account as verified so the
     * new 'verified' gate wouldn't lock anyone out on deploy. That's
     * no longer what's wanted: every account — new or existing —
     * must actually click a verification link before using the
     * platform. This is a new migration (rather than editing the old
     * one) so it applies correctly whether or not the earlier one
     * has already run in a given environment.
     */
    public function up(): void
    {
        DB::table('users')->update([
            'email_verified_at' => null,
            'email_verified' => false,
        ]);
    }

    public function down(): void
    {
        // Intentionally irreversible — see the earlier backfill
        // migration for the same reasoning.
    }
};
