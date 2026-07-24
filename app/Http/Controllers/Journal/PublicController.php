<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Manuscript;
use Illuminate\Http\Request;

/**
 * The Journal's public portal. No authentication required — every
 * published manuscript is visible to any visitor, exactly like a
 * journal's public article listing / abstract page.
 */
class PublicController extends Controller
{
    public function index(Request $request)
    {
        $articles = Manuscript::published()
            ->with('author')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('keywords', 'like', "%{$term}%");
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('modules.journal.public.index', compact('articles'));
    }

    public function show(Manuscript $manuscript)
    {
        abort_unless($manuscript->status === 'published', 404);

        $manuscript->load('author');

        return view('modules.journal.public.show', compact('manuscript'));
    }
}
