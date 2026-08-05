<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A byline credit on a manuscript, beyond the one corresponding
 * author (Manuscript::author). Not tied to a User account — most
 * co-authors never log into the platform at all.
 */
class ManuscriptCoAuthor extends Model
{
    protected $fillable = [
        'manuscript_id',
        'full_name',
        'email',
        'affiliation',
        'orcid',
        'position',
    ];

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }
}
