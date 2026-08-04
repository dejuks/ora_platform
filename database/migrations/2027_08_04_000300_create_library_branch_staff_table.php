<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scopes a Cataloger / Inventory Manager / Librarian (Physical) /
     * Acquisition Officer to one or more specific branches. A staff
     * member with *no* rows here is treated as unscoped (access to
     * every branch) — see User::canAccessLibraryBranch() — so
     * existing staff aren't locked out the moment this ships; the
     * Library Manager opts individual staff into branch restriction
     * by assigning them here.
     */
    public function up(): void
    {
        Schema::create('library_branch_staff', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('library_branches')->cascadeOnDelete();

            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['user_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_branch_staff');
    }
};
