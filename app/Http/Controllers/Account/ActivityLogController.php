<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * "Activity Log" — a read-only audit trail of what the current user
 * has done (logins, profile changes, etc). Reachable from the
 * top-right account menu.
 */
class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = Auth::user()
            ->activityLogs()
            ->paginate(20);

        return view('account.activity', compact('logs'));
    }
}
