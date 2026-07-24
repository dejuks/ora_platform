<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('ETB');

            $table->string('gateway')->default('chapa');
            $table->string('method')->default('chapa');
            // chapa, manual, waiver

            $table->string('status')->default('pending');
            // pending, completed, failed, waived

            $table->string('transaction_ref')->nullable()->unique();

            $table->text('notes')->nullable();
            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_payments');
    }
};
