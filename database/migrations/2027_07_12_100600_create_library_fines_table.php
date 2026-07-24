<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generated automatically when a loan is returned late (see
     * LibraryLoan::lateFeeFor()); collected or waived by the
     * Librarian (Physical), who "collects fines and fees" per the
     * circulation workflow.
     */
    public function up(): void
    {
        Schema::create('library_fines', function (Blueprint $table) {

            $table->id();

            $table->foreignId('library_loan_id')
                ->constrained('library_loans')
                ->cascadeOnDelete();

            $table->foreignId('library_member_id')
                ->constrained('library_members')
                ->cascadeOnDelete();

            $table->decimal('amount', 8, 2);
            $table->unsignedSmallInteger('days_overdue');

            $table->string('status')->default('unpaid');
            // unpaid, paid, waived

            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();

            $table->foreignId('waived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('waiver_reason')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_fines');
    }
};
