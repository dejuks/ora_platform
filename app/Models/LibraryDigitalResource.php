<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryDigitalResource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'resource_type',
        'author',
        'description',
        'subject',
        'keywords',
        'access_level',
        'status',
        'file_path',
        'file_original_name',
        'file_size',
        'mime_type',
        'cover_image',
        'uploaded_by',
        'published_by',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'downloads_count' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public const RESOURCE_TYPES = [
        'ebook' => 'eBook',
        'journal_article' => 'Journal Article',
        'paper' => 'Paper',
        'other' => 'Other',
    ];

    public const ACCESS_LEVELS = [
        'all_users' => 'All Library Users',
        'members_only' => 'Members Only',
        'staff_only' => 'Library Staff Only',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'submitted' => 'Submitted for Review',
        'published' => 'Published',
        'archived' => 'Archived',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function accessLevelLabel(): string
    {
        return self::ACCESS_LEVELS[$this->access_level] ?? $this->access_level;
    }

    public function resourceTypeLabel(): string
    {
        return self::RESOURCE_TYPES[$this->resource_type] ?? $this->resource_type;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * A Content Uploader / External Publisher (submit-digital-content)
     * only owns the record they uploaded, and only while it's still
     * in their hands — once the Digital Librarian publishes or
     * archives it, it's out of their control.
     */
    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->uploaded_by === $user->id;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-digital-collection')) {
            return true;
        }

        return $this->isOwnedBy($user)
            && $user->hasModulePermission('library', 'submit-digital-content')
            && in_array($this->status, ['draft', 'submitted'], true);
    }

    public function formattedFileSize(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 1).' '.$units[$i];
    }

    /**
     * Whether the given user can view/download this resource — the
     * "manage user access rights" side of the Digital Librarian's
     * job. Staff who can manage the collection can always preview a
     * draft/archived resource; everyone else needs it published and
     * within their access tier.
     */
    public function isAccessibleBy(?User $user): bool
    {
        if (! $user) {
            // Guest / not logged in — only the most open tier is
            // reachable, and only once it's actually published.
            // Matches the Member workflow's "access as guest (if
            // allowed)".
            return $this->isPublished() && $this->access_level === 'all_users';
        }

        if ($user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-digital-collection')) {
            return true;
        }

        if ($this->isOwnedBy($user) && $user->hasModulePermission('library', 'submit-digital-content')) {
            return true;
        }

        if (! $this->isPublished()) {
            return false;
        }

        return match ($this->access_level) {
            'members_only' => (bool) ($user->libraryMember?->status === 'active'),
            'staff_only' => $this->isLibraryStaff($user),
            default => true, // all_users — module access alone is enough
        };
    }

    protected function isLibraryStaff(User $user): bool
    {
        foreach (['manage-circulation', 'manage-circulation-policy', 'catalog-items', 'manage-inventory', 'approve-acquisitions', 'manage-digital-collection'] as $permission) {
            if ($user->hasModulePermission('library', $permission)) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
