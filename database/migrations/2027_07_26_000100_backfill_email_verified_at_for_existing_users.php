<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Every account created before today went through a signup flow
     * (self-registration, admin creation, seeders) that never sent a
     * verification email — email_verified_at is null for all of
     * them. Now that routes are gated behind the 'verified'
     * middleware, leaving it null would lock every existing user out
     * on deploy. Grandfather them in: only new registrations from
     * this point on actually have to click a verification link.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => now(),
                'email_verified' => true,
            ]);
    }

    public function down(): void
    {
        // Intentionally irreversible — we don't know which of these
        // rows were "really" verified vs grandfathered in, so there's
        // nothing meaningful to roll back to.
    }
};
