<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Root cause of "blocked by IP, but the account still works": a
 * WikiBlock created with target_type=ip only ever stored the IP.
 * There was nothing tying it back to the registered account that
 * was using that IP, so the account was never actually restricted.
 *
 * This adds the linkage needed for an "autoblock" — when a Sysop
 * blocks an IP, any account logged in from that IP is blocked too,
 * and lifting the parent block lifts the autoblock with it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wiki_blocks', function (Blueprint $table) {
            $table->foreignId('parent_block_id')
                ->nullable()
                ->after('ip_address')
                ->constrained('wiki_blocks')
                ->cascadeOnDelete();

            $table->boolean('is_autoblock')->default(false)->after('parent_block_id');

            $table->index('parent_block_id');
        });
    }

    public function down(): void
    {
        Schema::table('wiki_blocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_block_id');
            $table->dropColumn('is_autoblock');
        });
    }
};
