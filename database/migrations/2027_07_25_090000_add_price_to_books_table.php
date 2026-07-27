<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets the Digital Content Manager mark a published title as
     * 'for_sale' (see Book::ACCESS_TYPES) with a price, turning the
     * eBook public portal into a storefront for that title instead
     * of a free/institutional read. is_purchasable is a belt-and-
     * braces flag so a title can be taken off sale (out of print,
     * rights expired, etc.) without losing its price history.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('access_type');
            $table->boolean('is_purchasable')->default(false)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['price', 'is_purchasable']);
        });
    }
};
