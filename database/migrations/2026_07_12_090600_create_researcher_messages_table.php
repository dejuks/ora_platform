<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Direct, one-to-one internal messaging between two Researcher
     * Network members. Threaded by the pair of participants — the
     * conversation between user A and user B is every row where
     * (sender, recipient) matches that pair in either direction.
     */
    public function up(): void
    {
        Schema::create('researcher_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();

            $table->text('body');
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->index(['sender_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('researcher_messages');
    }
};
