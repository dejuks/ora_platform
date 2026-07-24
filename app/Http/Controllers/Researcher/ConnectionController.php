<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchConnection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Researcher / Member responsibility: "Search and connect with peers".
 */
class ConnectionController extends Controller
{
    /**
     * My connections: accepted network, plus incoming/outgoing
     * pending requests.
     */
    public function index()
    {
        $userId = Auth::id();

        $accepted = ResearchConnection::accepted()
            ->where(fn ($q) => $q->where('requester_id', $userId)->orWhere('addressee_id', $userId))
            ->with(['requester.researcherProfile', 'addressee.researcherProfile'])
            ->latest('responded_at')
            ->get();

        $incoming = ResearchConnection::pending()
            ->where('addressee_id', $userId)
            ->with('requester.researcherProfile')
            ->latest()
            ->get();

        $outgoing = ResearchConnection::pending()
            ->where('requester_id', $userId)
            ->with('addressee.researcherProfile')
            ->latest()
            ->get();

        return view('modules.researcher.connections.index', compact('accepted', 'incoming', 'outgoing'));
    }

    /**
     * Send a connection request to another member.
     */
    public function store(User $user)
    {
        $requesterId = Auth::id();

        abort_if($user->id === $requesterId, 422, 'You cannot connect with yourself.');
        abort_unless($user->hasModuleAccess('researcher'), 404);

        $exists = ResearchConnection::where(function ($q) use ($requesterId, $user) {
            $q->where('requester_id', $requesterId)->where('addressee_id', $user->id);
        })->orWhere(function ($q) use ($requesterId, $user) {
            $q->where('requester_id', $user->id)->where('addressee_id', $requesterId);
        })->first();

        if ($exists) {
            return back()->with('info', 'A connection already exists with this member.');
        }

        ResearchConnection::create([
            'requester_id' => $requesterId,
            'addressee_id' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Connection request sent.');
    }

    public function accept(ResearchConnection $connection)
    {
        $this->authorizeAddressee($connection);

        $connection->update(['status' => 'accepted', 'responded_at' => now()]);

        return back()->with('success', 'Connection accepted.');
    }

    public function decline(ResearchConnection $connection)
    {
        $this->authorizeAddressee($connection);

        $connection->update(['status' => 'declined', 'responded_at' => now()]);

        return back()->with('success', 'Connection declined.');
    }

    /**
     * Remove an accepted connection, or cancel a request you sent.
     */
    public function destroy(ResearchConnection $connection)
    {
        $userId = Auth::id();

        abort_unless(
            $connection->requester_id === $userId || $connection->addressee_id === $userId,
            403
        );

        $connection->delete();

        return back()->with('success', 'Connection removed.');
    }

    protected function authorizeAddressee(ResearchConnection $connection): void
    {
        abort_unless($connection->addressee_id === Auth::id(), 403);
        abort_unless($connection->status === 'pending', 422, 'This request has already been responded to.');
    }
}
