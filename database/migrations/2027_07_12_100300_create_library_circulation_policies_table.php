<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deliberately a single-row settings table (id = 1) rather than a
     * key/value store — there is exactly one circulation policy in
     * effect at a time, and the Library Manager (manage-circulation-policy)
     * is the only role that can change it.
     */
    public function up(): void
    {
        Schema::create('library_circulation_policies', function (Blueprint $table) {

            $table->id();

            $table->unsignedSmallInteger('loan_period_days')->default(14);
            $table->unsignedTinyInteger('max_renewals')->default(2);
            $table->decimal('fine_per_day', 8, 2)->default(0.50);
            $table->unsignedTinyInteger('hold_expiry_days')->default(3);

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_circulation_policies');
    }
};
