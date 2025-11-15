<?php

namespace App\Notifications\Feedback;

use App\Models\FeedbackThread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeedbackConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private FeedbackThread $thread,
        private string $visitorToken
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Feedback Received - ' . $this->thread->reference_id)
            ->greeting('Thank you for your feedback!')
            ->line('We have received your feedback and will review it shortly.')
            ->line('**Reference ID:** ' . $this->thread->reference_id)
            ->line('**Subject:** ' . $this->thread->subject)
            ->line('You can track the status of your feedback and view any responses by clicking the button below.')
            ->action('View Your Feedback', route('feedback.thread.view', ['token' => $this->visitorToken]))
            ->line('This secure link will allow you to view responses to your feedback.')
            ->line('Please keep this email for your records.')
            ->line('This link will expire in 30 days for security reasons.')
            ->line('If you did not submit this feedback, please ignore this email.');
    }

    public function toArray($notifiable): array
    {
        return [
            'thread_id' => $this->thread->id,
            'reference_id' => $this->thread->reference_id,
            'token' => $this->visitorToken,
        ];
    }
}