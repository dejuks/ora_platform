<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A research/interest group that members can create, join, and
     * discuss inside. Every group has a Group Moderator (defaults to
     * its creator) who approves membership and moderates discussions.
     */
    public function up(): void
    {
        Schema::create('research_groups', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('field_of_study')->nullable();

            $table->string('privacy')->default('public'); // public, private (approval required)
            $table->string('status')->default('active'); // active, archived

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // The member who currently moderates this group. Any Group
            // Moderator can be reassigned by a Platform Administrator.
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_groups');
    }
};
