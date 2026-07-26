<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Same single-row-settings pattern as journal_settings /
     * ebook_settings (id = 1, firstOrCreate on first use) — but for
     * platform-wide toggles that aren't specific to any one module.
     * Starts with just the email-verification requirement; add more
     * columns here later rather than creating a new table per toggle.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {

            $table->id();

            // Defaults true: matches the behavior already shipped —
            // every user (new and existing) must verify their email.
            // A Super Admin can turn this off from Settings.
            $table->boolean('require_email_verification')->default(true);

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
