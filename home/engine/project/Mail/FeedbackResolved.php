<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackResolved extends Mailable
{
    use Queueable, SerializesModels;

    public $feedback;

    public function __construct($feedback)
    {
        $this->feedback = $feedback;
    }

    public function build()
    {
        return $this->subject('Your Feedback Has Been Resolved - ' . $this->feedback->title)
            ->markdown('emails.feedback.resolved', [
                'feedback' => $this->feedback,
            ]);
    }
}
