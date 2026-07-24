<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An Administrator (Sysop) blocking either a registered vandal
     * (user_id) or a disruptive anonymous IP (ip_address) — exactly
     * one of the two is set per block. Indefinite when expires_at is
     * null.
     */
    public function up(): void
    {
        Schema::create('wiki_blocks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('ip_address')->nullable();

            $table->foreignId('blocked_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('reason');
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('lifted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('lifted_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_blocks');
    }
};
