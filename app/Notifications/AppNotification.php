<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * A single, general-purpose in-app notification.
 *
 * Fire it from anywhere:
 *
 *     $user->notify(new AppNotification(
 *         title: 'Manuscript accepted',
 *         message: 'Your manuscript "..." was accepted for publication.',
 *         url: route('journal.manuscripts.show', $manuscript),
 *         icon: 'bi-check-circle',
 *         type: 'success',
 *     ));
 *
 * It only stores itself in the database (drives the bell icon /
 * notifications page). Add the "mail" channel later if/when the
 * platform needs email too — the User model is already Notifiable.
 * Sent synchronously (not queued) so it shows up immediately even
 * without a queue worker running.
 */
class AppNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $icon = 'bi-bell',
        public string $type = 'info', // info | success | warning | danger
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'type' => $this->type,
        ];
    }
}
