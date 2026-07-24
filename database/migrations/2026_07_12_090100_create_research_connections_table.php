<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A LinkedIn-style connection request between two Researcher
     * Network members: requester sends it, addressee accepts /
     * declines. Once accepted both directions count as "connected".
     */
    public function up(): void
    {
        Schema::create('research_connections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('addressee_id')->constrained('users')->cascadeOnDelete();

            $table->string('status')->default('pending'); // pending, accepted, declined
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->unique(['requester_id', 'addressee_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_connections');
    }
};
