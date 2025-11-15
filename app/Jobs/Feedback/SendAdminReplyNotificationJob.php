<?php

namespace App\Jobs\Feedback;

use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use App\Notifications\Feedback\AdminReplyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAdminReplyNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [10, 30, 60];

    public function __construct(
        private FeedbackThread $thread,
        private FeedbackMessage $message
    ) {}

    public function handle(): void
    {
        try {
            $visitor = new class {
                public string $email;
                public function __construct(string $email) {
                    $this->email = $email;
                }
                public function routeNotificationForMail(): string {
                    return $this->email;
                }
            };

            $visitor->email = $this->thread->visitor_email;
            
            $visitor->notify(new AdminReplyNotification($this->thread, $this->message));
            
            Log::info('Admin reply notification sent successfully', [
                'thread_id' => $this->thread->id,
                'message_id' => $this->message->id,
                'reference_id' => $this->thread->reference_id,
                'visitor_email' => $this->thread->visitor_email,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send admin reply notification', [
                'thread_id' => $this->thread->id,
                'message_id' => $this->message->id,
                'reference_id' => $this->thread->reference_id,
                'visitor_email' => $this->thread->visitor_email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            if ($this->attempts() >= $this->tries) {
                Log::critical('Admin reply notification failed permanently', [
                    'thread_id' => $this->thread->id,
                    'message_id' => $this->message->id,
                    'reference_id' => $this->thread->reference_id,
                    'visitor_email' => $this->thread->visitor_email,
                ]);
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Admin reply notification job failed permanently', [
            'thread_id' => $this->thread->id,
            'message_id' => $this->message->id,
            'reference_id' => $this->thread->reference_id,
            'visitor_email' => $this->thread->visitor_email,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}