<?php

namespace App\Jobs\Feedback;

use App\Models\FeedbackThread;
use App\Notifications\Feedback\FeedbackConfirmationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendVisitorConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [10, 30, 60];

    public function __construct(
        private FeedbackThread $thread,
        private string $visitorToken
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
            
            $visitor->notify(new FeedbackConfirmationNotification($this->thread, $this->visitorToken));
            
            Log::info('Visitor confirmation sent successfully', [
                'thread_id' => $this->thread->id,
                'reference_id' => $this->thread->reference_id,
                'visitor_email' => $this->thread->visitor_email,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send visitor confirmation', [
                'thread_id' => $this->thread->id,
                'reference_id' => $this->thread->reference_id,
                'visitor_email' => $this->thread->visitor_email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            if ($this->attempts() >= $this->tries) {
                Log::critical('Visitor confirmation failed permanently', [
                    'thread_id' => $this->thread->id,
                    'reference_id' => $this->thread->reference_id,
                    'visitor_email' => $this->thread->visitor_email,
                ]);
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Visitor confirmation job failed permanently', [
            'thread_id' => $this->thread->id,
            'reference_id' => $this->thread->reference_id,
            'visitor_email' => $this->thread->visitor_email,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}