<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WikiBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Administrator (Sysop): block disruptive registered users or
 * anonymous IP addresses, and lift blocks once resolved.
 */
class BlockController extends Controller
{
    public function index()
    {
        $this->authorizePermission('moderate-content');

        $blocks = WikiBlock::with(['user', 'blockedBy', 'liftedBy'])
            ->latest()
            ->paginate(15);

        return view('modules.wiki.blocks.index', compact('blocks'));
    }

    public function create()
    {
        $this->authorizePermission('moderate-content');

        $users = User::orderBy('first_name')->get();

        return view('modules.wiki.blocks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('moderate-content');

        $data = $request->validate([
            'target_type' => ['required', 'in:user,ip'],
            'user_id' => ['required_if:target_type,user', 'nullable', 'exists:users,id'],
            'ip_address' => ['required_if:target_type,ip', 'nullable', 'ip'],
            'reason' => ['required', 'string'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        WikiBlock::create([
            'user_id' => $data['target_type'] === 'user' ? $data['user_id'] : null,
            'ip_address' => $data['target_type'] === 'ip' ? $data['ip_address'] : null,
            'blocked_by' => Auth::id(),
            'reason' => $data['reason'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('wiki.blocks.index')
            ->with('success', 'Block applied.');
    }

    public function lift(WikiBlock $block)
    {
        $this->authorizePermission('moderate-content');

        $block->update([
            'lifted_by' => Auth::id(),
            'lifted_at' => now(),
        ]);

        return back()->with('success', 'Block lifted.');
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
