<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a Sysop configure which categories exist (History, Fiction,
 * Education, ...) and turn one off without deleting it (so existing
 * articles keep their history intact).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wiki_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            $table->foreignId('created_by')->nullable()->after('sort_order')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wiki_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['is_active', 'sort_order']);
        });
    }
};
