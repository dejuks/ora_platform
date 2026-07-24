<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membership of a user inside a research group. For a public
     * group, joining is instant (status=approved). For a private
     * group, the request sits at status=pending until the Group
     * Moderator approves or rejects it.
     */
    public function up(): void
    {
        Schema::create('research_group_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('research_group_id')->constrained('research_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(['research_group_id', 'user_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_group_members');
    }
};
