<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * The fixed list of subject categories (Fiction, Literature,
 * Science, ...) an Author picks from when submitting a book, and
 * that visitors filter the public eBook listing by — same pattern as
 * JournalCategory for the Journal module.
 */
class BookCategory extends Model
{
    protected $table = 'ebook_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (BookCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
