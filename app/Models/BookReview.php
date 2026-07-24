<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReview extends Model
{
    protected $fillable = [
        'book_id',
        'reviewer_id',
        'status',
        'recommendation',
        'comments_to_author',
        'comments_to_editor',
        'assigned_at',
        'submitted_at',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'submitted_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public const RECOMMENDATIONS = [
        'accept' => 'Accept',
        'minor_revision' => 'Minor Revision',
        'major_revision' => 'Major Revision',
        'reject' => 'Reject',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
