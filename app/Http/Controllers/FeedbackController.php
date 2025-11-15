<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Requests\Feedback\StoreFeedbackReplyRequest;
use App\Jobs\Feedback\SendAdminNotificationJob;
use App\Jobs\Feedback\SendVisitorConfirmationJob;
use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('feedback.form');
    }

    public function store(StoreFeedbackRequest $request)
    {
        // Rate limiting: 3 submissions per hour per IP
        $executed = RateLimiter::attempt(
            'feedback-submission:' . $request->ip(),
            3,
            function () use ($request) {
                // Create the feedback thread
                $thread = FeedbackThread::create([
                    'visitor_name' => $request->visitor_name,
                    'visitor_email' => $request->visitor_email,
                    'subject' => $request->subject,
                    'visitor_ip' => $request->ip(),
                    'visitor_user_agent' => $request->userAgent(),
                ]);

                // Handle attachment if present
                $attachmentPath = null;
                $attachmentOriginalName = null;
                $attachmentMimeType = null;
                $attachmentSize = null;

                if ($attachment = $request->getSafeAttachment()) {
                    $filename = Str::uuid() . '.' . $attachment->getClientOriginalExtension();
                    $attachmentPath = $attachment->storeAs('feedback-attachments', $filename, 'local');
                    $attachmentOriginalName = $attachment->getClientOriginalName();
                    $attachmentMimeType = $attachment->getMimeType();
                    $attachmentSize = $attachment->getSize();
                }

                // Create the initial message
                FeedbackMessage::create([
                    'feedback_thread_id' => $thread->id,
                    'sender_type' => 'visitor',
                    'content' => $request->message,
                    'attachment_path' => $attachmentPath,
                    'attachment_original_name' => $attachmentOriginalName,
                    'attachment_mime_type' => $attachmentMimeType,
                    'attachment_size' => $attachmentSize,
                    'sent_at' => now(),
                ]);

                // Generate visitor token
                $visitorToken = $thread->generateVisitorToken();

                // Queue email notifications
                $adminEmail = config('feedback.admin_email', config('mail.from.address'));
                
                if ($adminEmail) {
                    SendAdminNotificationJob::dispatch($thread, $adminEmail);
                }

                SendVisitorConfirmationJob::dispatch($thread, $visitorToken);

                // Log the submission for audit
                \Log::info('Feedback submitted', [
                    'thread_id' => $thread->id,
                    'reference_id' => $thread->reference_id,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return redirect()->route('feedback.thankyou')
                    ->with('success', 'Thank you for your feedback! We have sent you a confirmation email with a link to track your submission.')
                    ->with('reference_id', $thread->reference_id);
            },
            3600 // 1 hour
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn('feedback-submission:' . $request->ip());
            
            return back()
                ->withErrors(['rate_limit' => 'Too many feedback submissions. Please try again in ' . $seconds . ' seconds.'])
                ->withInput();
        }
    }

    public function thankyou()
    {
        if (!session('success')) {
            return redirect()->route('feedback.form');
        }

        return view('feedback.thankyou', [
            'reference_id' => session('reference_id'),
        ]);
    }

    public function viewThread(Request $request, string $token)
    {
        try {
            $thread = FeedbackThread::findByVisitorToken($token);
            
            if (!$thread) {
                abort(404, 'Feedback thread not found or link has expired.');
            }

            // Load messages with most recent first
            $messages = $thread->messages()->orderBy('sent_at', 'asc')->get();

            return view('feedback.thread', [
                'thread' => $thread,
                'messages' => $messages,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error viewing feedback thread', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            
            abort(404, 'Invalid or expired link.');
        }
    }

    public function reply(StoreFeedbackReplyRequest $request, string $token)
    {
        try {
            $thread = FeedbackThread::findByVisitorToken($token);
            
            if (!$thread) {
                abort(404, 'Feedback thread not found or link has expired.');
            }

            // Check if thread is closed
            if ($thread->status === 'closed' || $thread->status === 'archived') {
                return back()->withErrors(['error' => 'This feedback thread is closed and cannot accept new messages.']);
            }

            // Rate limiting: 5 replies per hour per thread
            $executed = RateLimiter::attempt(
                'feedback-reply:' . $thread->id,
                5,
                function () use ($request, $thread) {
                    // Create visitor reply
                    FeedbackMessage::create([
                        'feedback_thread_id' => $thread->id,
                        'sender_type' => 'visitor',
                        'content' => $request->message,
                        'sent_at' => now(),
                    ]);

                    // Mark thread as open if it was closed
                    if ($thread->status === 'closed') {
                        $thread->markAsOpen();
                    }

                    // Log the reply
                    \Log::info('Visitor replied to feedback', [
                        'thread_id' => $thread->id,
                        'reference_id' => $thread->reference_id,
                        'ip' => $request->ip(),
                    ]);

                    return back()->with('success', 'Your reply has been sent successfully.');
                },
                3600 // 1 hour
            );

            if (!$executed) {
                $seconds = RateLimiter::availableIn('feedback-reply:' . $thread->id);
                
                return back()
                    ->withErrors(['rate_limit' => 'Too many replies. Please try again in ' . $seconds . ' seconds.'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Error submitting feedback reply', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            
            return back()->withErrors(['error' => 'An error occurred while submitting your reply. Please try again.']);
        }
    }

    public function downloadAttachment(Request $request, FeedbackMessage $message)
    {
        try {
            if (!$message->hasAttachment()) {
                abort(404, 'Attachment not found.');
            }

            $path = $message->getAttachmentPath();
            
            if (!file_exists($path)) {
                abort(404, 'Attachment file not found.');
            }

            return response()->download(
                $path,
                $message->attachment_original_name,
                [
                    'Content-Type' => $message->attachment_mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $message->attachment_original_name . '"',
                ]
            );

        } catch (\Exception $e) {
            \Log::error('Error downloading attachment', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            abort(404, 'Attachment not found.');
        }
    }
}