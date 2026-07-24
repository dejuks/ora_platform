<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The core entity of the Oromo Wikipedia module: an article created
     * and edited by Registered Editors, moderated (protected/deleted/
     * restored) by Administrators (Sysops), with final governance
     * sitting with the Bureaucrat & Global Steward via the existing
     * module-admin / role tooling.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {

            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');

            $table->string('status')->default('draft');
            // draft, published

            $table->string('protection_level')->default('none');
            // none, semi (autoconfirmed/registered editors only), full (sysop+ only)

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('last_edited_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('protected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('protected_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('restored_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('restored_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('protection_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
