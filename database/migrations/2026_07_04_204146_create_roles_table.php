<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A role always belongs to exactly one module (Journal's "Reviewer"
     * is a different row from a future module's "Reviewer"). This is
     * what makes roles dynamic: Super Admin (or a module admin with the
     * 'manage-roles' permission) can create/edit/delete roles per
     * module through the UI, with zero code changes.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('module_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->boolean('is_admin_role')->default(false);
            $table->boolean('is_system')->default(false);

            $table->timestamps();

            $table->unique(['module_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};