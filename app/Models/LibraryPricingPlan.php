<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LibraryPricingPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'resource_type',
        'amount',
        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mirrors App\Models\LibraryDigitalResource::RESOURCE_TYPES —
     * kept as its own copy here (rather than a shared constant) since
     * a pricing plan intentionally also allows "any type" (null),
     * which the resource itself never does.
     */
    public const RESOURCE_TYPES = [
        'ebook' => 'eBook',
        'journal_article' => 'Journal Article',
        'paper' => 'Paper',
        'other' => 'Other',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function resources()
    {
        return $this->hasMany(LibraryDigitalResource::class, 'pricing_plan_id');
    }

    public function purchases()
    {
        return $this->hasMany(LibraryResourcePurchase::class, 'pricing_plan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function resourceTypeLabel(): string
    {
        return $this->resource_type
            ? (self::RESOURCE_TYPES[$this->resource_type] ?? $this->resource_type)
            : 'Any resource type';
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'plan';
        $slug = $base;
        $i = 2;

        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Plans usable for a given resource type: either scoped to that
     * exact type, or open to any type (resource_type = null).
     */
    public function scopeForResourceType($query, string $resourceType)
    {
        return $query->where(function ($q) use ($resourceType) {
            $q->whereNull('resource_type')->orWhere('resource_type', $resourceType);
        });
    }
}
