<?php

namespace App\Notifications;

use App\Models\LoginLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLogin extends Notification implements ShouldQueue
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
            ->subject('New Device Login Detected')
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your admin account was just logged in from a new device.')
            ->line('Details:')
            ->line('Device: ' . ($this->loginLog->device_type ?? 'Unknown'))
            ->line('Browser: ' . ($this->loginLog->browser_name ?? 'Unknown') . ' ' . ($this->loginLog->browser_version ?? ''))
            ->line('Operating System: ' . ($this->loginLog->os_name ?? 'Unknown') . ' ' . ($this->loginLog->os_version ?? ''))
            ->line('IP Address: ' . $this->loginLog->ip_address)
            ->line('Location: ' . ($this->loginLog->city ? $this->loginLog->city . ', ' : '') . ($this->loginLog->country_name ?? 'Unknown'))
            ->line('Time: ' . $this->loginLog->created_at->format('Y-m-d H:i:s'))
            ->line('If this was you, you can safely ignore this email.')
            ->line('If you did not authorize this login, please change your password and contact your administrator immediately.')
            ->action('Manage Account', route('admin.dashboard'))
            ->line('For security purposes, we notify you of logins from new devices.');
    }
}
