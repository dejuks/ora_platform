<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('join_requests', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Applicant
            |--------------------------------------------------------------------------
            */

            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Interest
            |--------------------------------------------------------------------------
            |
            | Which module the visitor picked on the "Join ORA" form,
            | if any — nullable because someone may just want to join
            | the Association generally, without picking a module yet.
            |
            */

            $table->foreignId('module_id')
                ->nullable()
                ->constrained('modules')
                ->nullOnDelete();

            $table->text('message')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Review status
            |--------------------------------------------------------------------------
            |
            | A submission here does not create a user account by
            | itself — a Super Admin reviews it and, if approved,
            | creates the account and follows up with the applicant.
            |
            */

            $table->string('status', 20)->default('pending'); // pending | approved | declined

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_requests');
    }
};
