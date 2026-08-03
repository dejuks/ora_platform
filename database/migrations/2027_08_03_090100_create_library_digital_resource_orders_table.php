<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A reader's purchase of a priced digital library resource.
     * Mirrors ebook_orders exactly — one row per checkout attempt;
     * 'completed' is what unlocks the download. Free resources never
     * get a row here at all; they go straight through
     * LibraryDigitalResource::isAccessibleBy() same as before.
     */
    public function up(): void
    {
        Schema::create('library_digital_resource_orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_digital_resource_id')
                ->constrained('library_digital_resources')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('ETB');

            $table->string('gateway')->default('chapa');
            $table->string('method')->default('chapa');
            // chapa, manual

            $table->string('status')->default('pending');
            // pending, completed, failed

            $table->string('transaction_ref')->nullable()->unique();

            $table->text('notes')->nullable();
            $table->json('gateway_response')->nullable();

            $table->unsignedInteger('download_count')->default(0);

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'library_digital_resource_id'], 'ldro_user_resource_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_digital_resource_orders');
    }
};
