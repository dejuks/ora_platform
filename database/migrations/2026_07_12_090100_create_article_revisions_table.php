<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One immutable row per saved edit — the article's revision
     * history. The IP address / user agent are captured on every
     * edit so the Oversighter / CheckUser role has something to
     * inspect in serious abuse cases, and so any revision containing
     * private data can be suppressed (hidden from public view) for
     * GDPR-style compliance without deleting the record entirely.
     */
    public function up(): void
    {
        Schema::create('article_revisions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('article_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->longText('content');
            $table->string('edit_summary')->nullable();

            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->boolean('is_suppressed')->default(false);
            $table->foreignId('suppressed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('suppressed_at')->nullable();
            $table->text('suppression_reason')->nullable();

            $table->timestamps();

            $table->index(['article_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_revisions');
    }
};
