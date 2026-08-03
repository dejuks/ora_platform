<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Assigns a fee (library_pricing_plans) to a digital resource. Set
     * null on delete rather than restrict — if the Library Manager
     * deletes a plan, resources that used it simply fall back to free
     * (governed by access_level alone) instead of the deletion being
     * blocked.
     */
    public function up(): void
    {
        Schema::table('library_digital_resources', function (Blueprint $table) {
            $table->foreignId('pricing_plan_id')
                ->nullable()
                ->after('access_level')
                ->constrained('library_pricing_plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('library_digital_resources', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pricing_plan_id');
        });
    }
};
