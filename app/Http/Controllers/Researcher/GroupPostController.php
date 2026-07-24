<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchGroup;
use App\Models\ResearchGroupComment;
use App\Models\ResearchGroupPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Group discussions/forum. Any approved group member can post and
 * comment; the Group Moderator can pin, lock, or remove a post to
 * enforce community guidelines.
 */
class GroupPostController extends Controller
{
    public function store(Request $request, ResearchGroup $group)
    {
        $this->authorizeMember($group);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $data['research_group_id'] = $group->id;
        $data['user_id'] = Auth::id();
        $data['status'] = 'published';

        ResearchGroupPost::create($data);

        return back()->with('success', 'Discussion posted.');
    }

    public function comment(Request $request, ResearchGroup $group, ResearchGroupPost $post)
    {
        abort_unless($post->research_group_id === $group->id, 404);

        $this->authorizeMember($group);

        abort_if($post->is_locked, 422, 'This discussion is locked for new replies.');

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        ResearchGroupComment::create([
            'research_group_post_id' => $post->id,
            'user_id' => Auth::id(),
            'body' => $data['body'],
            'status' => 'published',
        ]);

        return back()->with('success', 'Reply posted.');
    }

    public function pin(ResearchGroup $group, ResearchGroupPost $post)
    {
        $this->authorizeModerator($group);

        $post->update(['is_pinned' => ! $post->is_pinned]);

        return back()->with('success', $post->is_pinned ? 'Post pinned.' : 'Post unpinned.');
    }

    public function lock(ResearchGroup $group, ResearchGroupPost $post)
    {
        $this->authorizeModerator($group);

        $post->update(['is_locked' => ! $post->is_locked]);

        return back()->with('success', $post->is_locked ? 'Discussion locked.' : 'Discussion unlocked.');
    }

    public function destroy(ResearchGroup $group, ResearchGroupPost $post)
    {
        $this->authorizeModerator($group);

        $post->update([
            'status' => 'removed',
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
        ]);

        return back()->with('success', 'Post removed for violating community guidelines.');
    }

    protected function authorizeMember(ResearchGroup $group): void
    {
        abort_unless(
            $group->isMember(Auth::id()) || $group->isModerator(Auth::user()),
            403,
            'You must be an approved member of this group to participate.'
        );
    }

    protected function authorizeModerator(ResearchGroup $group): void
    {
        abort_unless($group->isModerator(Auth::user()), 403);
    }
}
