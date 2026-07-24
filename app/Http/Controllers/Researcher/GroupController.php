<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchGroup;
use App\Models\ResearchGroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Group Moderator responsibility: "Approve and manage research group
 * memberships, moderate group discussions, enforce community
 * guidelines." Any Researcher / Member can create and join groups;
 * approving private-group membership and moderating discussions is
 * gated to whoever holds the 'manage-network-groups' permission (the
 * Group Moderator role) or is that specific group's assigned
 * moderator.
 */
class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchGroup::active()->withCount(['approvedMembers as members_count']);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('field_of_study', 'like', "%{$search}%");
            });
        }

        $groups = $query->latest()->paginate(12)->withQueryString();

        return view('modules.researcher.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('modules.researcher.groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'privacy' => ['required', 'in:'.implode(',', array_keys(ResearchGroup::PRIVACY_LEVELS))],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['created_by'] = Auth::id();
        $data['moderator_id'] = Auth::id();
        $data['status'] = 'active';

        $group = ResearchGroup::create($data);

        // Creator joins their own group automatically.
        ResearchGroupMember::create([
            'research_group_id' => $group->id,
            'user_id' => Auth::id(),
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('researcher.groups.show', $group)
            ->with('success', 'Group created.');
    }

    public function show(ResearchGroup $group)
    {
        $user = Auth::user();

        $group->load(['creator', 'moderator']);

        $membership = ResearchGroupMember::where('research_group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();

        $posts = $group->posts()->visible()->with(['author', 'comments.author'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(10);

        $pendingMembers = $group->isModerator($user)
            ? $group->members()->where('status', 'pending')->with('user')->get()
            : collect();

        return view('modules.researcher.groups.show', compact('group', 'membership', 'posts', 'pendingMembers'));
    }

    public function edit(ResearchGroup $group)
    {
        abort_unless($group->isModerator(Auth::user()), 403);

        return view('modules.researcher.groups.edit', compact('group'));
    }

    public function update(Request $request, ResearchGroup $group)
    {
        abort_unless($group->isModerator(Auth::user()), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'privacy' => ['required', 'in:'.implode(',', array_keys(ResearchGroup::PRIVACY_LEVELS))],
            'status' => ['required', 'in:active,archived'],
            'moderator_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $group->update($data);

        return redirect()->route('researcher.groups.show', $group)->with('success', 'Group updated.');
    }

    /**
     * Join a group. Public groups grant approved membership instantly;
     * private groups create a pending request for the moderator.
     */
    public function join(ResearchGroup $group)
    {
        $userId = Auth::id();

        $existing = ResearchGroupMember::where('research_group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            return back()->with('info', 'You have already requested or joined this group.');
        }

        $status = $group->privacy === 'public' ? 'approved' : 'pending';

        ResearchGroupMember::create([
            'research_group_id' => $group->id,
            'user_id' => $userId,
            'status' => $status,
            'approved_by' => $status === 'approved' ? $userId : null,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);

        return back()->with('success', $status === 'approved'
            ? 'You joined the group.'
            : 'Your request to join has been sent to the group moderator.');
    }

    public function leave(ResearchGroup $group)
    {
        ResearchGroupMember::where('research_group_id', $group->id)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'You left the group.');
    }

    /**
     * Group Moderator: approve a pending membership request.
     */
    public function approveMember(ResearchGroup $group, User $user)
    {
        abort_unless($group->isModerator(Auth::user()), 403);

        ResearchGroupMember::where('research_group_id', $group->id)
            ->where('user_id', $user->id)
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return back()->with('success', 'Member approved.');
    }

    /**
     * Group Moderator: reject a pending membership request, or
     * remove a member for a community-guidelines violation.
     */
    public function removeMember(ResearchGroup $group, User $user)
    {
        abort_unless($group->isModerator(Auth::user()), 403);

        ResearchGroupMember::where('research_group_id', $group->id)
            ->where('user_id', $user->id)
            ->delete();

        return back()->with('success', 'Member removed from the group.');
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (ResearchGroup::where('slug', $slug)->exists()) {
            $slug = "{$base}-".$i++;
        }

        return $slug;
    }
}
