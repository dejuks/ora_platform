<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A reader's purchase of a 'for_sale' eBook. Deliberately separate
     * from ebook_payments (that table is the Book Processing Charge
     * an AUTHOR pays to get published — a completely different payer,
     * payee, and purpose). One row per checkout attempt, same as
     * ebook_payments/journal_payments — 'completed' is what unlocks
     * the download and adds the title to the buyer's "My Digital
     * Library".
     */
    public function up(): void
    {
        Schema::create('ebook_orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

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
            $table->index(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_orders');
    }
};
