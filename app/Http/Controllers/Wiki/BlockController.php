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

        $block = WikiBlock::create([
            'user_id' => $data['target_type'] === 'user' ? $data['user_id'] : null,
            'ip_address' => $data['target_type'] === 'ip' ? $data['ip_address'] : null,
            'blocked_by' => Auth::id(),
            'reason' => $data['reason'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        $autoblocked = 0;
        if ($data['target_type'] === 'ip') {
            $autoblocked = $this->autoblockAccountsBehindIp($block);
        }

        $message = 'Block applied.';
        if ($autoblocked > 0) {
            $message .= ' '.trans_choice(
                '1 account logged in from this IP was also suspended (autoblock).|:count accounts logged in from this IP were also suspended (autoblock).',
                $autoblocked,
                ['count' => $autoblocked]
            );
        }

        return redirect()
            ->route('wiki.blocks.index')
            ->with('success', $message);
    }

    public function lift(WikiBlock $block)
    {
        $this->authorizePermission('moderate-content');

        $block->update([
            'lifted_by' => Auth::id(),
            'lifted_at' => now(),
        ]);

        // Lifting an IP block also lifts every account it autoblocked,
        // so an admin never has to remember to clear both separately.
        $block->autoblocks()->active()->get()->each(function (WikiBlock $autoblock) {
            $autoblock->update([
                'lifted_by' => Auth::id(),
                'lifted_at' => now(),
            ]);
        });

        return back()->with('success', 'Block lifted.');
    }

    /**
     * An IP block alone doesn't stop a registered account that was
     * using that IP — it just keeps its own session/cookies and can
     * keep editing. Mirror MediaWiki's "autoblock": find any account
     * that most recently logged in from this IP and place a linked
     * block directly on the account, so it stays suspended even if
     * it later switches to a different IP.
     */
    protected function autoblockAccountsBehindIp(WikiBlock $ipBlock): int
    {
        $accounts = User::where('last_login_ip', $ipBlock->ip_address)->get();

        $count = 0;
        foreach ($accounts as $account) {
            $alreadyBlocked = WikiBlock::active()->where('user_id', $account->id)->exists();

            if ($alreadyBlocked) {
                continue;
            }

            WikiBlock::create([
                'user_id' => $account->id,
                'parent_block_id' => $ipBlock->id,
                'is_autoblock' => true,
                'blocked_by' => Auth::id(),
                'reason' => "Autoblock: account was logged in from blocked IP {$ipBlock->ip_address}. ({$ipBlock->reason})",
                'expires_at' => $ipBlock->expires_at,
            ]);

            $count++;
        }

        return $count;
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
