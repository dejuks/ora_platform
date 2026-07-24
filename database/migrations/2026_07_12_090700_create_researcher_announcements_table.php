<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Calls for papers, conferences, and platform news published by
     * the Event/Content Manager and shown to every Researcher Network
     * member.
     */
    public function up(): void
    {
        Schema::create('researcher_announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('type')->default('news'); // call_for_papers, conference, event, news
            $table->text('body');

            $table->string('location')->nullable();
            $table->string('link_url')->nullable();

            $table->timestamp('event_date')->nullable();
            $table->timestamp('submission_deadline')->nullable();

            $table->string('status')->default('draft'); // draft, published
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('researcher_announcements');
    }
};
