<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original 'status' string column ('Active'/'Inactive') never
     * matched the boolean writes/reads used elsewhere in the app. This
     * replaces it with a plain boolean 'is_active', consistent with the
     * rest of the schema (modules.is_active, etc).
     */
    public function up(): void
    {
        Schema::table('user_modules', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('user_modules', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_modules', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('user_modules', function (Blueprint $table) {
            $table->string('status')->default('Active')->after('is_admin');
        });
    }
};
