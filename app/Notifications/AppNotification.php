<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A single, general-purpose in-app + email notification.
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
 * Stores itself in the database (drives the bell icon / notifications
 * page) and, unless the user turned it off in Settings, also sends
 * an email. Sent synchronously (not queued) so it shows up
 * immediately even without a queue worker running.
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
        public string $actionLabel = 'View Details',
    ) {}

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable->notify_in_app ?? true) {
            $channels[] = 'database';
        }

        if (($notifiable->notify_email ?? true) && filter_var($notifiable->email ?? null, FILTER_VALIDATE_EMAIL)) {
            $channels[] = 'mail';
        }

        return $channels;
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

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hi '.($notifiable->first_name ?: $notifiable->full_name).',')
            ->line($this->message);

        if ($this->url) {
            $mail->action($this->actionLabel, $this->url);
        }

        return $mail->salutation('— '.config('app.name'));
    }
}
