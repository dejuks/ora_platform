<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a user turn in-app / email notifications on or off from
     * the new Settings page.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_in_app')->default(true)->after('profile_photo');
            $table->boolean('notify_email')->default(true)->after('notify_in_app');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_in_app', 'notify_email']);
        });
    }
};
