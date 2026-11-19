<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberWelcomePasswordSetup extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Welcome to BBHS Old Boys’ Association')
            ->greeting('Welcome, ' . ($notifiable->name ?? 'BBHS Alumnus') . '!')
            ->line('Your membership account has been created on the BBHS Old Boys’ Association portal.')
            ->line('To activate your account, set a secure password using the button below. The link is time-limited for your security.')
            ->action('Set Your Password', $url)
            ->line('After setting your password, sign in and complete the remaining details in your member profile.')
            ->line('If you were not expecting this invitation, you can ignore this email.')
            ->salutation('Baptist Boys’ High School Old Boys’ Association');
    }
}
