<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Account-level settings — currently notification preferences.
 * Reachable from the top-right account menu.
 */
class SettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('account.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'notify_in_app' => ['sometimes', 'boolean'],
            'notify_email' => ['sometimes', 'boolean'],
        ]);

        $user->update([
            'notify_in_app' => $request->boolean('notify_in_app'),
            'notify_email' => $request->boolean('notify_email'),
        ]);

        ActivityLogger::log('settings.updated', 'Updated notification preferences');

        return back()->with('status', 'Settings saved.');
    }
}
