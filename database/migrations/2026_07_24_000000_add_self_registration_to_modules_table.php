<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Self-Registration
            |--------------------------------------------------------------------------
            |
            | is_self_registerable: whether this module is offered as a checkbox
            | on the public registration form / "My Modules" self-service page.
            |
            | default_role_slug: the Role (scoped to this module) a user is
            | granted, active immediately, when they self-register into it.
            | Null means the module cannot be self-joined even if the flag
            | above is somehow set — the seeder always sets both together.
            |
            */

            $table->boolean('is_self_registerable')->default(false)->after('is_active');
            $table->string('default_role_slug', 60)->nullable()->after('is_self_registerable');

            $table->index('is_self_registerable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropIndex(['is_self_registerable']);
            $table->dropColumn(['is_self_registerable', 'default_role_slug']);
        });
    }
};
