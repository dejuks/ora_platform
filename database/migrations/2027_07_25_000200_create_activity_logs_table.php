<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A simple audit trail of what a user did and when — backs the
     * "Activity Log" page in the top-right account menu.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Short machine-friendly action key, e.g. "login", "logout",
            // "profile.updated", "password.changed".
            $table->string('action', 100);

            // Human-readable line shown in the UI,
            // e.g. "Updated profile photo".
            $table->string('description');

            // Optional polymorphic link to whatever the action was about
            // (a manuscript, a book, another user, ...).
            $table->nullableMorphs('subject');

            // Any extra structured detail (old/new values, etc).
            $table->json('properties')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
