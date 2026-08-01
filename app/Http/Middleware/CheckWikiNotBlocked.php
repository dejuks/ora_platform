<?php

namespace App\Http\Middleware;

use App\Models\WikiBlock; // adjust namespace to match your actual model
use Closure;
use Illuminate\Http\Request;

class CheckWikiNotBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $isBlocked = WikiBlock::query()
            ->where('user_id', $user->id)
            ->whereNull('lifted_at')          // not lifted
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($isBlocked) {
            abort(403, 'You are currently blocked from editing the wiki.');
        }

        return $next($request);
    }
}
