<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryBookCopy;
use App\Models\LibraryCategory;
use App\Notifications\AppNotification;
use App\Support\NotifiesPermissionHolders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Physical Library — the catalog side.
 *
 * A Cataloger creates the bibliographic record (title, author,
 * ISBN, DDC/LCC call number) and it starts life as
 * 'pending_acquisition'. Physical copies are added the same way —
 * also pending — and get barcode/RFID-tagged and moved to
 * 'available' by the Inventory Manager. The Library Manager gives
 * the final go-ahead (approve-acquisitions) that flips the title
 * to 'active' and puts its pending copies into circulation.
 */
class BookController extends Controller
{
    use NotifiesPermissionHolders;

    public function index(Request $request)
    {
        $user = Auth::user();

        // The acquisitions queue (titles cataloged but not yet
        // approved) is staff business, not part of the public
        // catalog a Member searches — mirrors the doc's separation
        // between "search catalog" (Member) and "approve
        // acquisitions" (Library Manager) / procurement (Acquisition
        // Officer) / cataloging (Cataloger) / tagging (Inventory
        // Manager).
        $canSeeAcquisitions = $user->isSuperAdmin()
            || $user->hasModulePermission('library', 'catalog-items')
            || $user->hasModulePermission('library', 'approve-acquisitions')
            || $user->hasModulePermission('library', 'manage-acquisitions')
            || $user->hasModulePermission('library', 'manage-inventory');

        $query = LibraryBook::withCount([
            'copies',
            'copies as available_copies_count' => fn ($q) => $q->where('status', 'available'),
        ])->latest();

        if (! $canSeeAcquisitions) {
            $query->where('status', '!=', 'pending_acquisition');
        } elseif ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->paginate(15)->withQueryString();

        $canCatalog = $user->hasModulePermission('library', 'catalog-items') || $user->isSuperAdmin();

        return view('modules.library.books.index', compact('books', 'canSeeAcquisitions', 'canCatalog'));
    }

    public function create()
    {
        $this->authorizePermission('catalog-items');

        $categories = LibraryCategory::active()->ordered()->get();

        return view('modules.library.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('catalog-items');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:32'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:64'],
            'call_number' => ['nullable', 'string', 'max:64'],
            'subject' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:library_categories,id'],
            'description' => ['nullable', 'string'],
        ]);

        $data['status'] = 'pending_acquisition';
        $data['cataloged_by'] = Auth::id();
        $data['created_by'] = Auth::id();

        $book = LibraryBook::create($data);

        $this->notifyPermissionHolders('library', 'approve-acquisitions', new AppNotification(
            title: 'New title pending acquisition',
            message: "\"{$book->title}\" was cataloged by {$book->catalogedBy->full_name} and is awaiting acquisition approval.",
            url: route('library.books.show', $book),
            icon: 'bi-journal-plus',
            type: 'info',
        ), excludeUserId: Auth::id());

        return redirect()
            ->route('library.books.show', $book)
            ->with('success', 'Title cataloged. Awaiting Library Manager approval before it enters circulation.');
    }

    public function show(LibraryBook $book)
    {
        $book->load(['catalogedBy', 'approvedBy', 'category', 'copies', 'holds.member.user']);

        return view('modules.library.books.show', compact('book'));
    }

    public function edit(LibraryBook $book)
    {
        $this->authorizePermission('catalog-items');

        $categories = LibraryCategory::active()->ordered()->get();

        return view('modules.library.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, LibraryBook $book)
    {
        $this->authorizePermission('catalog-items');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:32'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:64'],
            'call_number' => ['nullable', 'string', 'max:64'],
            'subject' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:library_categories,id'],
            'description' => ['nullable', 'string'],
        ]);

        $data['updated_by'] = Auth::id();

        $book->update($data);

        return redirect()
            ->route('library.books.show', $book)
            ->with('success', 'Catalog record updated.');
    }

    /**
     * Library Manager: approve a pending title for acquisition. Puts
     * the title and every pending copy attached to it into
     * circulation in one step.
     */
    public function approveAcquisition(LibraryBook $book)
    {
        $this->authorizePermission('approve-acquisitions');

        abort_unless($book->status === 'pending_acquisition', 422, 'This title has already been decided on.');

        $book->update([
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $book->copies()
            ->where('status', 'pending_acquisition')
            ->update(['status' => 'available', 'updated_by' => Auth::id()]);

        $book->catalogedBy?->notify(new AppNotification(
            title: 'Acquisition approved',
            message: "\"{$book->title}\" was approved and is now active in the catalog.",
            url: route('library.books.show', $book),
            icon: 'bi-check-circle',
            type: 'success',
        ));

        return back()->with('success', 'Acquisition approved. Title and its copies are now active.');
    }

    /**
     * Inventory Manager: add a new physical copy of an existing
     * title — generates a barcode if one isn't supplied and tags it.
     */
    public function storeCopy(Request $request, LibraryBook $book)
    {
        $this->authorizePermission('manage-inventory');

        $data = $request->validate([
            'barcode' => ['nullable', 'string', 'max:64', 'unique:library_book_copies,barcode'],
            'shelf_location' => ['nullable', 'string', 'max:64'],
            'condition' => ['required', 'in:good,worn,damaged'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['library_book_id'] = $book->id;
        $data['barcode'] = $data['barcode'] ?: strtoupper('LB-'.$book->id.'-'.Str::random(6));
        $data['status'] = $book->status === 'active' ? 'available' : 'pending_acquisition';
        $data['tagged_by'] = Auth::id();
        $data['created_by'] = Auth::id();

        LibraryBookCopy::create($data);

        return back()->with('success', 'Copy added and tagged.');
    }

    /**
     * Inventory Manager: record the outcome of a stocktaking check —
     * mark a copy lost, damaged, withdrawn, or back to available.
     */
    public function updateCopyStatus(Request $request, LibraryBookCopy $copy)
    {
        $this->authorizePermission('manage-inventory');

        $data = $request->validate([
            'status' => ['required', 'in:available,lost,damaged,withdrawn'],
            'condition' => ['nullable', 'in:good,worn,damaged'],
            'notes' => ['nullable', 'string'],
        ]);

        abort_if($copy->status === 'on_loan' && $data['status'] === 'available', 422, 'This copy is currently on loan.');

        $data['updated_by'] = Auth::id();

        $copy->update($data);

        return back()->with('success', 'Copy status updated.');
    }

    /**
     * Inventory Manager: a cross-title view of every physical copy,
     * for shelf reading / stocktaking audits — the doc's "conduct
     * shelf reading and stock taking" and "track missing/damaged
     * items" responsibilities, which a per-book view can't support.
     */
    public function copiesIndex(Request $request)
    {
        $this->authorizePermission('manage-inventory');

        $query = LibraryBookCopy::with('book')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                    ->orWhere('shelf_location', 'like', "%{$search}%")
                    ->orWhereHas('book', fn ($bq) => $bq->where('title', 'like', "%{$search}%"));
            });
        }

        $copies = $query->paginate(20)->withQueryString();

        return view('modules.library.copies.index', compact('copies'));
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
