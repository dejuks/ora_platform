<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Physical Library locations — Jimma, Adama, Finfinnee (Addis
     * Ababa), Shashamane, Bale Robe, Nekemte, and any others the
     * Library Manager (manage-settings) adds later. Every physical
     * copy (library_book_copies.branch_id) lives at exactly one of
     * these; a title's catalog record (library_books) itself stays
     * branch-agnostic since the same title can have copies at
     * several branches.
     */
    public function up(): void
    {
        Schema::create('library_branches', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('code')->unique();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_branches');
    }
};
