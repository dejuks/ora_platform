<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearcherMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Researcher / Member responsibility: "Participate in messaging,
 * forums, and groups." Simple direct messaging between two members.
 */
class MessageController extends Controller
{
    /**
     * Inbox: one row per conversation partner, most recent message first.
     */
    public function index()
    {
        $userId = Auth::id();

        $partnerIds = ResearcherMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->get()
            ->map(fn ($m) => $m->sender_id === $userId ? $m->recipient_id : $m->sender_id)
            ->unique();

        $conversations = $partnerIds->map(function ($partnerId) use ($userId) {
            $last = ResearcherMessage::between($userId, $partnerId)->latest()->first();
            $unread = ResearcherMessage::where('sender_id', $partnerId)
                ->where('recipient_id', $userId)
                ->whereNull('read_at')
                ->count();

            return [
                'partner' => User::find($partnerId),
                'last_message' => $last,
                'unread_count' => $unread,
            ];
        })->filter(fn ($c) => $c['partner'] !== null)
            ->sortByDesc(fn ($c) => $c['last_message']?->created_at)
            ->values();

        return view('modules.researcher.messages.index', compact('conversations'));
    }

    public function show(User $user)
    {
        $userId = Auth::id();

        abort_unless($user->hasModuleAccess('researcher'), 404);

        $messages = ResearcherMessage::between($userId, $user->id)
            ->with(['sender', 'recipient'])
            ->oldest()
            ->get();

        ResearcherMessage::where('sender_id', $user->id)
            ->where('recipient_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('modules.researcher.messages.show', compact('messages', 'user'));
    }

    public function store(Request $request, User $user)
    {
        abort_if($user->id === Auth::id(), 422, 'You cannot message yourself.');
        abort_unless($user->hasModuleAccess('researcher'), 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        ResearcherMessage::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $user->id,
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Message sent.');
    }
}
