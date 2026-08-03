<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Full CRUD list of fee rules the Library Manager (manage-settings)
     * maintains — e.g. "Standard eBook Access", "Premium Journal
     * Article", "Licensed Paper Package" — each with its own amount
     * and currency. A digital resource is assigned one of these plans
     * (see library_digital_resources.pricing_plan_id); if it has none,
     * it is free and access is governed purely by access_level.
     *
     * resource_type, when set, restricts which kind of digital
     * resource this plan may be attached to (ebook, journal_article,
     * paper, other) — left null to allow any type.
     */
    public function up(): void
    {
        Schema::create('library_pricing_plans', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('resource_type')->nullable();
            // null = applies to any resource type; otherwise one of
            // ebook, journal_article, paper, other

            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('ETB');

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['is_active', 'resource_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_pricing_plans');
    }
};
