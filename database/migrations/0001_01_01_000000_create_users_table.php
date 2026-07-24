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
        Schema::create('users', function (Blueprint $table) {

            // Primary Key
            $table->id();

            /**
             * Basic Information
             */
            $table->string('employee_no', 30)->nullable()->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);

            $table->string('username', 50)->unique();
            $table->string('email', 150)->unique();
            $table->string('phone', 30)->nullable()->unique();

            /**
             * Personal Information
             */
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->date('date_of_birth')->nullable();

            /**
             * Profile
             */
            $table->string('profile_photo')->nullable();

            /**
             * Authentication
             */
            $table->string('password');
            $table->rememberToken();

            /**
             * Account Status
             */
            $table->enum('status', [
                'Active',
                'Inactive',
                'Suspended',
                'Locked'
            ])->default('Active');

            /**
             * Email Verification
             */
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();

            /**
             * Login Information
             */
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            /**
             * Security
             */
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            /**
             * Audit
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /**
             * Soft Delete
             */
            $table->softDeletes();

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Indexes
             */
            $table->index('username');
            $table->index('email');
            $table->index('status');
            $table->index('employee_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};