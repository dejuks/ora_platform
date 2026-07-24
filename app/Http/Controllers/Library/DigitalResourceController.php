<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryDigitalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * The Digital Library.
 *
 * Two ways in: the Digital Librarian (manage-digital-collection) can
 * upload, review, and publish directly. A Content Uploader / External
 * Publisher (submit-digital-content) can only prepare a draft and
 * submit it — the Digital Librarian still reviews for quality and
 * compliance and does the actual publish/archive step.
 */
class DigitalResourceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $canManage = $user->hasModulePermission('library', 'manage-digital-collection');
        $canSubmit = $user->hasModulePermission('library', 'submit-digital-content');

        $query = LibraryDigitalResource::query()->latest();

        if ($canManage) {
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
        } elseif ($canSubmit) {
            // A Content Uploader / External Publisher sees the
            // published collection plus the submissions they own,
            // regardless of status — they need to track their own
            // drafts and pending reviews.
            $query->where(function ($q) use ($user) {
                $q->where('status', 'published')
                    ->orWhere('uploaded_by', $user->id);
            });

            if ($request->get('status') === 'mine') {
                $query->where('uploaded_by', $user->id);
            }
        } else {
            $query->published();
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $resources = $query->paginate(15)->withQueryString();

        // Filter out anything this viewer isn't allowed to see (a
        // members_only or staff_only resource they don't qualify
        // for). Kept simple with a post-filter since access rules
        // depend on relationships the query builder can't easily
        // express in one pass.
        $resources->setCollection(
            $resources->getCollection()->filter(fn ($resource) => $canManage || $resource->isAccessibleBy($user))->values()
        );

        return view('modules.library.digital-resources.index', compact('resources', 'canManage', 'canSubmit'));
    }

    public function create()
    {
        $this->authorizeUploadAccess();

        return view('modules.library.digital-resources.create');
    }

    public function store(Request $request)
    {
        $this->authorizeUploadAccess();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'resource_type' => ['required', 'in:ebook,journal_article,paper,other'],
            'author' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'access_level' => ['required', 'in:all_users,members_only,staff_only'],
            'file' => ['required', 'file', 'mimes:pdf,epub,doc,docx,txt', 'max:51200'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $file = $request->file('file');

        $data['file_path'] = $file->store('library/digital-resources', 'public');
        $data['file_original_name'] = $file->getClientOriginalName();
        $data['file_size'] = $file->getSize();
        $data['mime_type'] = $file->getMimeType();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('library/digital-resources/covers', 'public');
        }

        $data['status'] = 'draft';
        $data['uploaded_by'] = Auth::id();
        $data['created_by'] = Auth::id();

        $resource = LibraryDigitalResource::create($data);

        return redirect()
            ->route('library.digital-resources.show', $resource)
            ->with('success', 'Resource uploaded as a draft. Submit it for review once metadata and access rights are confirmed.');
    }

    public function show(LibraryDigitalResource $resource)
    {
        $this->authorizeView($resource);

        if ($resource->isPublished() && ! Auth::user()->hasModulePermission('library', 'manage-digital-collection')) {
            $resource->increment('views_count');
        }

        return view('modules.library.digital-resources.show', compact('resource'));
    }

    public function edit(LibraryDigitalResource $resource)
    {
        abort_unless($resource->canBeEditedBy(Auth::user()), 403, 'You do not have permission to edit this resource.');

        return view('modules.library.digital-resources.edit', compact('resource'));
    }

    public function update(Request $request, LibraryDigitalResource $resource)
    {
        abort_unless($resource->canBeEditedBy(Auth::user()), 403, 'You do not have permission to edit this resource.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'resource_type' => ['required', 'in:ebook,journal_article,paper,other'],
            'author' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'access_level' => ['required', 'in:all_users,members_only,staff_only'],
            'file' => ['nullable', 'file', 'mimes:pdf,epub,doc,docx,txt', 'max:51200'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }

            $file = $request->file('file');
            $data['file_path'] = $file->store('library/digital-resources', 'public');
            $data['file_original_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        if ($request->hasFile('cover_image')) {
            if ($resource->cover_image) {
                Storage::disk('public')->delete($resource->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('library/digital-resources/covers', 'public');
        }

        $data['updated_by'] = Auth::id();

        $resource->update($data);

        return redirect()
            ->route('library.digital-resources.show', $resource)
            ->with('success', 'Resource updated.');
    }

    /**
     * Digital Librarian: the record has been reviewed for quality
     * and compliance — make it visible to its intended audience.
     */
    public function publish(LibraryDigitalResource $resource)
    {
        $this->authorizePermission('manage-digital-collection');

        abort_if($resource->status === 'published', 422, 'This resource is already published.');
        abort_unless($resource->file_path, 422, 'Upload a file before publishing this resource.');

        $resource->update([
            'status' => 'published',
            'published_by' => Auth::id(),
            'published_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Resource published.');
    }

    public function archive(LibraryDigitalResource $resource)
    {
        $this->authorizePermission('manage-digital-collection');

        abort_unless($resource->status === 'published', 422, 'Only published resources can be archived.');

        $resource->update(['status' => 'archived', 'updated_by' => Auth::id()]);

        return back()->with('success', 'Resource archived and hidden from the collection.');
    }

    /**
     * Gated file download — access rights enforced the same way as
     * viewing, plus a usage-monitoring increment (the Digital
     * Librarian's "monitor usage statistics" responsibility).
     */
    public function download(LibraryDigitalResource $resource)
    {
        $this->authorizeView($resource);

        abort_unless($resource->file_path, 404, 'No file available for this resource.');

        if (! Auth::user()->hasModulePermission('library', 'manage-digital-collection')) {
            $resource->increment('downloads_count');
        }

        return Storage::disk('public')->download($resource->file_path, $resource->file_original_name ?: $resource->title);
    }

    /**
     * Content Uploader / External Publisher: hand a prepared draft
     * to the Digital Librarian for the "review for quality and
     * compliance" step, before it can be published.
     */
    public function submitForReview(LibraryDigitalResource $resource)
    {
        $user = Auth::user();

        abort_unless(
            $resource->isOwnedBy($user) && $user->hasModulePermission('library', 'submit-digital-content'),
            403,
            'You do not have permission to submit this resource.'
        );

        abort_unless($resource->status === 'draft', 422, 'Only a draft can be submitted for review.');
        abort_unless($resource->file_path, 422, 'Upload a file before submitting this resource for review.');

        $resource->update(['status' => 'submitted', 'updated_by' => Auth::id()]);

        return back()->with('success', 'Submitted for the Digital Librarian\'s review.');
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function authorizeUploadAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user->isSuperAdmin()
                || $user->hasModulePermission('library', 'manage-digital-collection')
                || $user->hasModulePermission('library', 'submit-digital-content'),
            403,
            'You do not have permission to do this.'
        );
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    protected function authorizeView(LibraryDigitalResource $resource): void
    {
        abort_unless($resource->isAccessibleBy(Auth::user()), 403, 'You do not have access to this resource.');
    }
}
