<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One profile per user — the public-facing side of the
     * Researchers' Network: affiliation, credentials, research
     * interests, and links a member curates about themselves.
     * Created automatically (empty) the moment a user gets access
     * to the Researcher Network module, then filled in by the member.
     */
    public function up(): void
    {
        Schema::create('researcher_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->string('headline')->nullable(); // e.g. "Associate Professor of Linguistics"
            $table->text('bio')->nullable();

            $table->string('institution')->nullable(); // affiliation
            $table->string('department')->nullable();
            $table->string('position_title')->nullable();

            $table->string('academic_degree')->nullable(); // highest credential, e.g. PhD
            $table->text('credentials')->nullable(); // free text: degrees, certifications

            $table->string('field_of_study')->nullable();
            $table->text('research_interests')->nullable(); // comma separated / free text keywords
            $table->text('publications')->nullable(); // free-text list / links to own works

            $table->string('city')->nullable();
            $table->string('country')->nullable();

            $table->string('website_url')->nullable();
            $table->string('orcid_id')->nullable();
            $table->string('linkedin_url')->nullable();

            $table->boolean('is_public')->default(true); // visible in directory / search

            $table->timestamps();

            $table->index(['institution']);
            $table->index(['field_of_study']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('researcher_profiles');
    }
};
