<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BbhsPlatformNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $category = 'general',
        public string $url = '/notifications',
        public array $extra = [],
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('bbhs.notifications.email', false) && ! ($this->extra['database_only'] ?? false) && ! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'url' => $this->safeUrl($this->url),
            'extra' => $this->extra,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello ' . ($notifiable->name ?? 'BBHS Member'))
            ->line($this->message)
            ->action('Open BBHS Portal', url($this->safeUrl($this->url)))
            ->line('Baptist Boys’ High School Old Boys’ Association');
    }

    private function safeUrl(string $url): string
    {
        return str_starts_with($url, '/') && ! str_starts_with($url, '//') ? $url : '/notifications';
    }
}
