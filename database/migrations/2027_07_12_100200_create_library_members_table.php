<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A library_member is the patron profile behind an ORA user account
     * — separate from their 'library-member' module role, because a
     * user needs an actual borrowing record (membership number, type,
     * loan limit) before they can check anything out.
     */
    public function up(): void
    {
        Schema::create('library_members', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('membership_no')->unique();
            $table->string('member_type')->default('student');
            // student, staff, faculty, external

            $table->string('status')->default('active');
            // active, suspended, expired

            $table->unsignedTinyInteger('max_active_loans')->default(3);

            $table->timestamp('joined_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
