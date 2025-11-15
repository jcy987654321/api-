<?php

namespace App\Jobs\Feedback;

use App\Models\FeedbackThread;
use App\Notifications\Feedback\NewFeedbackNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAdminNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = [10, 30, 60];

    public function __construct(
        private FeedbackThread $thread,
        private string $adminEmail
    ) {}

    public function handle(): void
    {
        try {
            $admin = new class {
                public string $email;
                public function __construct(string $email) {
                    $this->email = $email;
                }
                public function routeNotificationForMail(): string {
                    return $this->email;
                }
            };

            $admin->email = $this->adminEmail;
            
            $admin->notify(new NewFeedbackNotification($this->thread));
            
            Log::info('Admin notification sent successfully', [
                'thread_id' => $this->thread->id,
                'reference_id' => $this->thread->reference_id,
                'admin_email' => $this->adminEmail,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'thread_id' => $this->thread->id,
                'reference_id' => $this->thread->reference_id,
                'admin_email' => $this->adminEmail,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            if ($this->attempts() >= $this->tries) {
                Log::critical('Admin notification failed permanently', [
                    'thread_id' => $this->thread->id,
                    'reference_id' => $this->thread->reference_id,
                    'admin_email' => $this->adminEmail,
                ]);
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('Admin notification job failed permanently', [
            'thread_id' => $this->thread->id,
            'reference_id' => $this->thread->reference_id,
            'admin_email' => $this->adminEmail,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}