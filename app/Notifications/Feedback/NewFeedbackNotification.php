<?php

namespace App\Notifications\Feedback;

use App\Models\FeedbackThread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewFeedbackNotification extends Notification
{
    use Queueable;

    public function __construct(
        private FeedbackThread $thread
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Feedback Received: ' . $this->thread->subject)
            ->greeting('Hello Administrator,')
            ->line('A new feedback has been submitted on your platform.')
            ->line('**Reference ID:** ' . $this->thread->reference_id)
            ->line('**From:** ' . ($this->thread->visitor_name ?: 'Anonymous') . ' <' . $this->thread->visitor_email . '>')
            ->line('**Subject:** ' . $this->thread->subject)
            ->line('**Status:** ' . ucfirst($this->thread->status))
            ->line('**Submitted:** ' . $this->thread->created_at->format('Y-m-d H:i:s'))
            ->line('**IP Address:** ' . $this->thread->visitor_ip)
            ->action('View Feedback', route('admin.feedback.show', $this->thread))
            ->line('Please review and respond to this feedback in a timely manner.')
            ->line('This is an automated notification. Please do not reply to this email.');
    }

    public function toArray($notifiable): array
    {
        return [
            'thread_id' => $this->thread->id,
            'reference_id' => $this->thread->reference_id,
            'subject' => $this->thread->subject,
            'status' => $this->thread->status,
        ];
    }
}