<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets the Digital Librarian mark a resource as paid — mirrors
     * add_price_to_books_table for the Ebook module's own storefront.
     * is_purchasable is a belt-and-braces flag so a resource can be
     * taken off sale without losing its price history. A resource
     * with price = null/0 (or is_purchasable = false) stays free and
     * subject only to its existing access_level gate.
     */
    public function up(): void
    {
        Schema::table('library_digital_resources', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('access_level');
            $table->string('currency', 10)->default('ETB')->after('price');
            $table->boolean('is_purchasable')->default(false)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('library_digital_resources', function (Blueprint $table) {
            $table->dropColumn(['price', 'currency', 'is_purchasable']);
        });
    }
};
