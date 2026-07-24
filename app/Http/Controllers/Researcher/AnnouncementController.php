<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearcherAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Event/Content Manager responsibility: "Publish and update
 * information on calls for papers, conferences, and events; notify
 * users of relevant updates; keep platform content up-to-date."
 * Every member can read published announcements; only whoever holds
 * the 'publish-announcements' permission (Event/Content Manager) can
 * create, edit, or publish them.
 */
class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $canManage = $user->hasModulePermission('researcher', 'publish-announcements');

        $query = ResearcherAnnouncement::query();

        if (! $canManage) {
            $query->published();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $announcements = $query->latest()->paginate(10)->withQueryString();

        return view('modules.researcher.announcements.index', compact('announcements', 'canManage'));
    }

    public function create()
    {
        $this->authorizeManage();

        return view('modules.researcher.announcements.create');
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $data = $this->validated($request);
        $data['created_by'] = Auth::id();
        $data['status'] = 'draft';

        $announcement = ResearcherAnnouncement::create($data);

        if ($request->boolean('publish_now')) {
            $announcement->update([
                'status' => 'published',
                'published_by' => Auth::id(),
                'published_at' => now(),
            ]);
        }

        return redirect()->route('researcher.announcements.index')->with('success', 'Announcement saved.');
    }

    public function show(ResearcherAnnouncement $announcement)
    {
        $user = Auth::user();

        abort_if(
            $announcement->status !== 'published' && ! $user->hasModulePermission('researcher', 'publish-announcements'),
            404
        );

        return view('modules.researcher.announcements.show', compact('announcement'));
    }

    public function edit(ResearcherAnnouncement $announcement)
    {
        $this->authorizeManage();

        return view('modules.researcher.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, ResearcherAnnouncement $announcement)
    {
        $this->authorizeManage();

        $announcement->update($this->validated($request));

        return redirect()->route('researcher.announcements.index')->with('success', 'Announcement updated.');
    }

    public function publish(ResearcherAnnouncement $announcement)
    {
        $this->authorizeManage();

        $announcement->update([
            'status' => 'published',
            'published_by' => Auth::id(),
            'published_at' => now(),
        ]);

        return back()->with('success', 'Announcement published to all members.');
    }

    public function destroy(ResearcherAnnouncement $announcement)
    {
        $this->authorizeManage();

        $announcement->delete();

        return redirect()->route('researcher.announcements.index')->with('success', 'Announcement removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(ResearcherAnnouncement::TYPES))],
            'body' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'event_date' => ['nullable', 'date'],
            'submission_deadline' => ['nullable', 'date'],
        ]);
    }

    protected function authorizeManage(): void
    {
        abort_unless(Auth::user()->hasModulePermission('researcher', 'publish-announcements'), 403);
    }
}
