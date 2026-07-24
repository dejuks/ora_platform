<?php
namespace App\Models\Wiki;
use Illuminate\Database\Eloquent\Model;
class ContactMessage extends Model
{
    protected $table = 'wiki_contact_messages';
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'read_at',
    ];
    protected $casts = [
        'read_at' => 'datetime',
    ];
}
