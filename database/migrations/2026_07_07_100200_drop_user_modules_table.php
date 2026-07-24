<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * user_modules only ever supported "member" or "module admin" via
     * a single is_admin flag. That's now fully replaced by
     * user_module_roles, which supports any number of named,
     * permission-bearing roles per module. Nothing in the codebase
     * references this table anymore.
     */
    public function up(): void
    {
        Schema::dropIfExists('user_modules');
    }

    public function down(): void
    {
        Schema::create('user_modules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'module_id']);
        });
    }
};
