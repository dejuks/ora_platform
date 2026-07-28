<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\LibraryHold;
use App\Services\ModuleEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The physical Library's public portal. Anyone can search and browse
 * the catalog, logged in or not — same reasoning as the Journal /
 * Ebook / Repository public portals. Reserving a title is self-
 * service for the Member workflow ("place holds/reservations"), but
 * still requires a Library membership record, so reserve() enrolls
 * the visitor on the spot rather than sending them off to a separate
 * sign-up flow (same one-click pattern as My Modules /
 * AuthorEnrollmentController). Checkout and return of the physical
 * copy still happen in person at the circulation desk — see
 * Library\CirculationController, untouched by this portal.
 */
class PublicController extends Controller
{
    public function __construct(protected ModuleEnrollmentService $enrollment)
    {
    }

    public function index(Request $request)
    {
        $query = LibraryBook::active()
            ->withCount([
                'copies as available_copies_count' => fn ($q) => $q->where('status', 'available'),
                'copies as total_copies_count',
            ])
            ->with('category');

        if ($request->filled('q')) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('author', 'like', "%{$term}%")
                    ->orWhere('subject', 'like', "%{$term}%")
                    ->orWhere('isbn', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category')) {
            $query->inCategory($request->string('category'));
        }

        if ($request->filled('letter') && $request->get('letter') !== 'ALL') {
            $query->titleStartsWith(strtoupper($request->string('letter')));
        }

        $sort = $request->get('sort', 'az');
        match ($sort) {
            'za' => $query->orderByDesc('title'),
            'latest' => $query->latest('created_at'),
            default => $query->orderBy('title'),
        };

        $books = $query->paginate(12)->withQueryString();

        $categories = LibraryCategory::active()
            ->withCount(['books' => fn ($q) => $q->active()])
            ->ordered()
            ->get();

        $letters = range('A', 'Z');

        return view('modules.library.public.index', compact('books', 'categories', 'letters'));
    }

    public function show(LibraryBook $book)
    {
        abort_unless($book->status === 'active', 404);

        $book->loadCount([
            'copies as available_copies_count' => fn ($q) => $q->where('status', 'available'),
            'copies as total_copies_count',
        ]);

        $book->load('category');

        $member = Auth::user()?->libraryMember;

        $myHold = $member
            ? $book->holds()->where('library_member_id', $member->id)->whereIn('status', ['pending', 'ready'])->first()
            : null;

        return view('modules.library.public.show', compact('book', 'myHold'));
    }

    /**
     * Visitor: reserve a title with no copy currently available.
     * Auto-enrolls the logged-in visitor as a Library member first if
     * they aren't one yet — mirrors HoldController@store's rules
     * exactly (holds are a queue for when nothing is on the shelf; if
     * a copy is available there's nothing to reserve, just come in).
     */
    public function reserve(LibraryBook $book)
    {
        abort_unless($book->status === 'active', 404);

        $user = Auth::user();
        $member = $user->libraryMember;

        if (! $member) {
            $this->enrollment->enroll($user, 'library');
            $member = $user->libraryMember()->first();
        }

        if (! $member) {
            return back()->with('error', 'We could not set up a library membership for your account. Please contact the library.');
        }

        abort_unless($member->status === 'active', 403, 'Your library membership is not active.');

        if ($book->hasAvailableCopy()) {
            return back()->with('info', 'A copy is available right now — no reservation needed, just visit the circulation desk to check it out.');
        }

        $exists = LibraryHold::where('library_book_id', $book->id)
            ->where('library_member_id', $member->id)
            ->whereIn('status', ['pending', 'ready'])
            ->exists();

        if ($exists) {
            return back()->with('info', 'You already have a reservation on this title.');
        }

        LibraryHold::create([
            'library_book_id' => $book->id,
            'library_member_id' => $member->id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('success', 'Reserved. We will notify you when a copy is ready for pickup — bring your membership number to the circulation desk.');
    }
}
