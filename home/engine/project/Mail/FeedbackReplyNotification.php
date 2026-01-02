<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $feedback;
    public $reply;

    public function __construct($user, $feedback, $reply)
    {
        $this->user = $user;
        $this->feedback = $feedback;
        $this->reply = $reply;
    }

    public function build()
    {
        return $this->subject('New Reply to Your Feedback - ' . $this->feedback->title)
            ->markdown('emails.feedback.reply', [
                'user' => $this->user,
                'feedback' => $this->feedback,
                'reply' => $this->reply,
            ]);
    }
}
