<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminUserCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private User $user)
    {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Admin Account Created')
            ->greeting("Hello {$this->user->name}!")
            ->line('An admin account has been created for you.')
            ->line('Email: ' . $this->user->email)
            ->line('Please set your password by clicking the button below.')
            ->action('Set Password', route('password.reset', ['token' => 'temp']))
            ->line('If you did not request this, please contact your administrator.');
    }
}
