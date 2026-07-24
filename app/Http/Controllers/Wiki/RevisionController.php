<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\ArticleRevision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Oversighter / CheckUser: the only role that can see every
 * revision's IP address / user agent, and the only role that can
 * suppress a revision containing private data (hidden from public
 * view, kept for compliance/audit purposes rather than deleted).
 */
class RevisionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('suppress-revisions');

        $query = ArticleRevision::with(['article', 'editor', 'suppressedBy'])->latest();

        if ($request->query('suppressed') === '1') {
            $query->where('is_suppressed', true);
        }

        $revisions = $query->paginate(20)->withQueryString();

        return view('modules.wiki.revisions.index', compact('revisions'));
    }

    public function suppress(Request $request, ArticleRevision $revision)
    {
        $this->authorizePermission('suppress-revisions');

        $data = $request->validate([
            'suppression_reason' => ['required', 'string'],
        ]);

        $revision->update([
            'is_suppressed' => true,
            'suppressed_by' => Auth::id(),
            'suppressed_at' => now(),
            'suppression_reason' => $data['suppression_reason'],
        ]);

        return back()->with('success', 'Revision suppressed.');
    }

    public function unsuppress(ArticleRevision $revision)
    {
        $this->authorizePermission('suppress-revisions');

        $revision->update([
            'is_suppressed' => false,
            'suppressed_by' => null,
            'suppressed_at' => null,
            'suppression_reason' => null,
        ]);

        return back()->with('success', 'Revision restored to public view.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('wiki', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
