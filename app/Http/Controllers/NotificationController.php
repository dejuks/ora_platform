<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * In-app notifications — the bell icon in the top menu and the
 * "View all notifications" page it links to.
 */
class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark one notification read and send the user to whatever it
     * points at (or back to the notifications list if it has no link).
     */
    public function open(Request $request, string $notification)
    {
        $notif = Auth::user()->notifications()->findOrFail($notification);

        if (! $notif->read_at) {
            $notif->markAsRead();
        }

        return redirect($notif->data['url'] ?? route('notifications.index'));
    }

    public function markRead(string $notification)
    {
        $notif = Auth::user()->notifications()->findOrFail($notification);
        $notif->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
