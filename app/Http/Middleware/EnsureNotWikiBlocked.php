<?php

namespace App\Http\Middleware;

use App\Models\WikiBlock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * WikiBlock records were only ever written (BlockController@store),
 * never read anywhere else — so a Sysop blocking a user or an IP had
 * zero effect on what that user/IP could actually do. This middleware
 * is what makes a block real.
 *
 * It also closes the specific gap that was reported: a block created
 * with target_type=ip only stores ip_address (user_id is null), so a
 * registered account logged in from that IP was never restricted.
 * Here we check the block against BOTH the authenticated user_id AND
 * the current request IP, so an IP block stops that account's actions
 * immediately, for as long as they use that IP. BlockController also
 * creates a linked "autoblock" on the account itself, so the account
 * stays suspended even if it switches to a different IP afterwards.
 */
class EnsureNotWikiBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $userId = Auth::id();
        $ip = $request->ip();

        $block = WikiBlock::active()
            ->where(function ($query) use ($userId, $ip) {
                $query->where('ip_address', $ip);

                if ($userId) {
                    $query->orWhere('user_id', $userId);
                }
            })
            ->latest()
            ->first();

        if ($block) {
            abort(403, $this->message($block));
        }

        return $next($request);
    }

    protected function message(WikiBlock $block): string
    {
        $scope = $block->user_id ? 'Your account is blocked' : 'Your IP address is blocked';
        $until = $block->expires_at
            ? 'until '.$block->expires_at->format('M j, Y H:i').' UTC'
            : 'indefinitely';

        return "{$scope} from editing {$until}. Reason: {$block->reason}";
    }
}
