<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Fan a notification out to "whoever currently holds this permission"
 * in a module — e.g. every Associate Editor, whoever that turns out
 * to be — instead of hardcoding a role name or a specific user.
 */
trait NotifiesPermissionHolders
{
    protected function notifyPermissionHolders(
        string $moduleCode,
        string $permission,
        Notification $notification,
        ?int $excludeUserId = null,
    ): void {
        $recipients = User::whereHas('moduleRoles', function ($q) use ($moduleCode, $permission) {
            $q->whereHas('module', fn ($m) => $m->where('code', $moduleCode))
                ->whereHas('permissions', fn ($p) => $p->where('slug', $permission));
        })
            ->when($excludeUserId, fn ($q) => $q->where('id', '!=', $excludeUserId))
            ->get();

        if ($recipients->isNotEmpty()) {
            NotificationFacade::send($recipients, $notification);
        }
    }
}
