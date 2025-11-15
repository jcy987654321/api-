<?php

namespace App\Notifications\Feedback;

use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        private FeedbackThread $thread,
        private FeedbackMessage $message
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Response to Your Feedback - ' . $this->thread->reference_id)
            ->greeting('Hello,')
            ->line('You have received a response to your feedback submission.')
            ->line('**Reference ID:** ' . $this->thread->reference_id)
            ->line('**Subject:** ' . $this->thread->subject)
            ->line('')
            ->line('**Response from Administrator:**')
            ->line($this->message->content)
            ->line('')
            ->action('View Full Conversation', route('feedback.thread.view', ['token' => $this->generateVisitorToken()]))
            ->line('You can view the complete conversation and continue the discussion by clicking the button above.')
            ->line('This secure link will allow you to respond to the administrator.')
            ->line('If you did not submit this feedback, please ignore this email.');
    }

    public function toArray($notifiable): array
    {
        return [
            'thread_id' => $this->thread->id,
            'message_id' => $this->message->id,
            'reference_id' => $this->thread->reference_id,
        ];
    }

    private function generateVisitorToken(): string
    {
        return encrypt([
            'thread_id' => $this->thread->id,
            'reference_id' => $this->thread->reference_id,
            'email' => $this->thread->visitor_email,
            'expires_at' => now()->addDays(30),
        ]);
    }
}