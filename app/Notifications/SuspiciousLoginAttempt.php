<?php

namespace App\Notifications;

use App\Models\LoginLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginAttempt extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private LoginLog $loginLog)
    {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suspicious Login Attempt on Your Account')
            ->greeting("Hello {$notifiable->name}!")
            ->line('We detected a suspicious login attempt on your admin account.')
            ->line('Details:')
            ->line('IP Address: ' . $this->loginLog->ip_address)
            ->line('Location: ' . ($this->loginLog->city ? $this->loginLog->city . ', ' : '') . ($this->loginLog->country_name ?? 'Unknown'))
            ->line('Device: ' . ($this->loginLog->device_type ?? 'Unknown'))
            ->line('Browser: ' . ($this->loginLog->browser_name ?? 'Unknown'))
            ->line('Time: ' . $this->loginLog->created_at->format('Y-m-d H:i:s'))
            ->line('If this was you, you can ignore this email.')
            ->line('If you did not authorize this login, please change your password immediately.')
            ->action('Secure Account', route('admin.dashboard'))
            ->line('If you continue to receive these emails, contact your administrator.');
    }
}
