<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookPayment extends Model
{
    protected $fillable = [
        'book_id',
        'author_id',
        'amount',
        'currency',
        'gateway',
        'method',
        'status',
        'transaction_ref',
        'notes',
        'gateway_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
