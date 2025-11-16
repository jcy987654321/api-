<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountLocked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private int $lockoutMinutes = 30)
    {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Locked')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your admin account has been temporarily locked due to multiple failed login attempts.')
            ->line('Your account will automatically unlock in ' . $this->lockoutMinutes . ' minutes.')
            ->line('If you believe this is an error or you need immediate access, please contact your administrator.')
            ->action('Contact Support', route('admin.dashboard'))
            ->line('We take security very seriously and this action was taken to protect your account.');
    }
}
