<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchConnection;
use App\Models\ResearchGroup;
use App\Models\ResearchGroupMember;
use App\Models\ResearcherAnnouncement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'connections' => ResearchConnection::accepted()
                ->where(fn ($q) => $q->where('requester_id', $user->id)->orWhere('addressee_id', $user->id))
                ->count(),
            'pending_requests' => ResearchConnection::pending()->where('addressee_id', $user->id)->count(),
            'groups' => ResearchGroupMember::where('user_id', $user->id)->where('status', 'approved')->count(),
            'profile_complete' => (bool) optional($user->researcherProfile)->headline,
        ];

        $announcements = ResearcherAnnouncement::published()->latest()->take(5)->get();

        return view('modules.researcher.dashboard', [
            'moduleLabel' => 'Researcher Network',
            'stats' => $stats,
            'announcements' => $announcements,
        ]);
    }

    public function admin()
    {
        $stats = [
            'total_members' => User::whereHas('moduleRoles', fn ($q) => $q->whereHas('module', fn ($m) => $m->where('code', 'researcher')))->count(),
            'total_groups' => ResearchGroup::count(),
            'pending_group_requests' => ResearchGroupMember::where('status', 'pending')->count(),
            'draft_announcements' => ResearcherAnnouncement::where('status', 'draft')->count(),
            'published_announcements' => ResearcherAnnouncement::where('status', 'published')->count(),
        ];

        return view('modules.researcher.admin-dashboard', [
            'moduleLabel' => 'Researcher Network',
            'stats' => $stats,
        ]);
    }
}
