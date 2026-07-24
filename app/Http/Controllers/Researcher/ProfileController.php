<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchConnection;
use App\Models\ResearcherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Researcher / Member responsibility: "Create and maintain detailed
 * profiles (credentials, affiliations, publications)" and "Search
 * and connect with peers". This controller covers a member's own
 * profile, the searchable member directory, and viewing another
 * member's public profile.
 */
class ProfileController extends Controller
{
    /**
     * Searchable directory of every member with a public profile.
     */
    public function index(Request $request)
    {
        $query = ResearcherProfile::with('user')
            ->where('is_public', true)
            ->whereHas('user', fn ($q) => $q->where('status', 'Active'));

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('institution', 'like', "%{$search}%")
                    ->orWhere('field_of_study', 'like', "%{$search}%")
                    ->orWhere('research_interests', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('institution')) {
            $query->where('institution', 'like', '%'.$request->string('institution').'%');
        }

        $profiles = $query->paginate(12)->withQueryString();

        return view('modules.researcher.members.index', compact('profiles'));
    }

    /**
     * My own profile — edit form.
     */
    public function edit()
    {
        $profile = $this->myProfile();

        return view('modules.researcher.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = $this->myProfile();

        $data = $request->validate([
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'institution' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'position_title' => ['nullable', 'string', 'max:255'],
            'academic_degree' => ['nullable', 'string', 'max:255'],
            'credentials' => ['nullable', 'string'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'research_interests' => ['nullable', 'string'],
            'publications' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'orcid_id' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $data['is_public'] = $request->boolean('is_public');

        $profile->update($data);

        return redirect()
            ->route('researcher.profile.edit')
            ->with('success', 'Profile updated.');
    }

    /**
     * View another member's public profile.
     */
    public function show(User $user)
    {
        abort_unless($user->hasModuleAccess('researcher'), 404);

        $profile = $user->researcherProfile ?: $user->researcherProfile()->create([]);

        $viewer = Auth::user();

        abort_if(
            ! $profile->is_public && $viewer->id !== $user->id && ! $viewer->isModuleAdmin('researcher'),
            403,
            'This member has kept their profile private.'
        );

        $connectionStatus = null;

        if ($viewer->id !== $user->id) {
            $connection = ResearchConnection::where(function ($q) use ($viewer, $user) {
                $q->where('requester_id', $viewer->id)->where('addressee_id', $user->id);
            })->orWhere(function ($q) use ($viewer, $user) {
                $q->where('requester_id', $user->id)->where('addressee_id', $viewer->id);
            })->first();

            $connectionStatus = $connection?->status;
        }

        return view('modules.researcher.profile.show', compact('profile', 'user', 'connectionStatus'));
    }

    protected function myProfile(): ResearcherProfile
    {
        $user = Auth::user();

        return $user->researcherProfile ?: $user->researcherProfile()->create([]);
    }
}
