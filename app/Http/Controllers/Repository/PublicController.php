<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Controller;
use App\Models\RepositoryItem;
use Illuminate\Http\Request;

/**
 * The Repository's public portal. No authentication required —
 * every published item is discoverable and citable by any visitor,
 * exactly like an institutional repository's public record page.
 * Restricted items are browsable and citable but gate the actual
 * file download behind login (enforced in RepositoryItemController).
 * A live embargo hides an item entirely until it lifts.
 */
class PublicController extends Controller
{
    public function index(Request $request)
    {
        $items = RepositoryItem::accessibleNow()
            ->with('depositor')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q');
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', "%{$term}%")
                        ->orWhere('authors', 'like', "%{$term}%")
                        ->orWhere('keywords', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('resource_type', $request->string('type'));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $resourceTypes = RepositoryItem::RESOURCE_TYPES;

        return view('modules.repository.public.index', compact('items', 'resourceTypes'));
    }

    public function show(RepositoryItem $item)
    {
        abort_unless($item->status === 'published', 404);
        abort_if($item->embargo_until && $item->embargo_until->isFuture(), 404);

        $item->increment('views_count');

        $item->load('depositor');

        return view('modules.repository.public.show', compact('item'));
    }
}
