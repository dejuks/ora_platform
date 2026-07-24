<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearcherProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'institution',
        'department',
        'position_title',
        'academic_degree',
        'credentials',
        'field_of_study',
        'research_interests',
        'publications',
        'city',
        'country',
        'website_url',
        'orcid_id',
        'linkedin_url',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
