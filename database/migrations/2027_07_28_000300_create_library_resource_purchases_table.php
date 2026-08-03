<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per checkout attempt for a paid digital resource, via
     * Chapa (https://chapa.co). Mirrors ebook_payments/journal_payments
     * — see App\Services\ChapaService and
     * Library\DigitalResourcePaymentController for the initialize ->
     * pay -> return/webhook -> verify flow.
     *
     * amount/currency are snapshotted at checkout time so a later
     * change to the plan (or the plan being deleted) never rewrites
     * what was actually charged on a completed purchase.
     */
    public function up(): void
    {
        Schema::create('library_resource_purchases', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_digital_resource_id')
                ->constrained('library_digital_resources')
                ->cascadeOnDelete();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('pricing_plan_id')
                ->nullable()
                ->constrained('library_pricing_plans')
                ->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('ETB');

            $table->string('gateway')->default('chapa');
            $table->string('method')->nullable();
            // card, bank_transfer, mobile_money

            $table->string('status')->default('pending');
            // pending, completed, failed

            $table->string('transaction_ref')->unique();
            $table->json('gateway_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['library_digital_resource_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_resource_purchases');
    }
};
