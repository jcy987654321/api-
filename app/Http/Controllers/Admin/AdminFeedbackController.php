<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\StoreFeedbackReplyRequest;
use App\Http\Requests\Feedback\UpdateFeedbackStatusRequest;
use App\Jobs\Feedback\SendAdminReplyNotificationJob;
use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminFeedbackController extends Controller
{
    public function __construct()
    {
        // Note: In production, uncomment the auth middleware when you have a user system
        // $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = FeedbackThread::with(['messages' => function ($query) {
            $query->latest('sent_at')->limit(1);
        }]);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by reference ID, email, or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('visitor_name', 'like', "%{$search}%");
                // Note: We can't search encrypted fields directly without decrypting
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $threads = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => FeedbackThread::count(),
            'open' => FeedbackThread::open()->count(),
            'closed' => FeedbackThread::closed()->count(),
            'archived' => FeedbackThread::archived()->count(),
            'today' => FeedbackThread::whereDate('created_at', today())->count(),
        ];

        return view('admin.feedback.index', compact('threads', 'stats'));
    }

    public function show(FeedbackThread $thread)
    {
        $thread->load(['messages' => function ($query) {
            $query->orderBy('sent_at', 'asc');
        }]);

        return view('admin.feedback.show', compact('thread'));
    }

    public function reply(StoreFeedbackReplyRequest $request, FeedbackThread $thread)
    {
        try {
            // Create admin reply
            $message = FeedbackMessage::create([
                'feedback_thread_id' => $thread->id,
                'sender_type' => 'admin',
                'content' => $request->message,
                'sent_at' => now(),
            ]);

            // Update thread status if needed
            if ($thread->status === 'closed') {
                $thread->markAsOpen();
            }

            // Send email notification to visitor if requested
            if ($request->boolean('send_email_notification', true)) {
                SendAdminReplyNotificationJob::dispatch($thread, $message);
            }

            // Log the admin reply
            \Log::info('Admin replied to feedback', [
                'thread_id' => $thread->id,
                'message_id' => $message->id,
                'reference_id' => $thread->reference_id,
                'admin_id' => auth()->id(),
                'send_notification' => $request->boolean('send_email_notification'),
            ]);

            return back()
                ->with('success', 'Reply sent successfully.')
                ->withFragment('message-' . $message->id);

        } catch (\Exception $e) {
            \Log::error('Error sending admin reply', [
                'thread_id' => $thread->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to send reply. Please try again.'])
                ->withInput();
        }
    }

    public function updateStatus(UpdateFeedbackStatusRequest $request, FeedbackThread $thread)
    {
        try {
            $thread->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
            ]);

            // Log the status change
            \Log::info('Feedback thread status updated', [
                'thread_id' => $thread->id,
                'reference_id' => $thread->reference_id,
                'old_status' => $thread->getOriginal('status'),
                'new_status' => $request->status,
                'admin_id' => auth()->id(),
            ]);

            return back()
                ->with('success', 'Thread status updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Error updating feedback status', [
                'thread_id' => $thread->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to update status. Please try again.'])
                ->withInput();
        }
    }

    public function redactMessage(Request $request, FeedbackMessage $message)
    {
        try {
            $message->redact();

            // Log the redaction
            \Log::info('Feedback message redacted', [
                'message_id' => $message->id,
                'thread_id' => $message->feedback_thread_id,
                'admin_id' => auth()->id(),
            ]);

            return back()
                ->with('success', 'Message has been redacted.')
                ->withFragment('message-' . $message->id);

        } catch (\Exception $e) {
            \Log::error('Error redacting message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to redact message. Please try again.']);
        }
    }

    public function deleteMessage(FeedbackMessage $message)
    {
        try {
            $threadId = $message->feedback_thread_id;
            
            // Delete attachment if exists
            if ($message->hasAttachment()) {
                $message->deleteAttachment();
            }

            $message->delete();

            // Log the deletion
            \Log::info('Feedback message deleted', [
                'message_id' => $message->id,
                'thread_id' => $threadId,
                'admin_id' => auth()->id(),
            ]);

            return back()
                ->with('success', 'Message has been deleted.');

        } catch (\Exception $e) {
            \Log::error('Error deleting message', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to delete message. Please try again.']);
        }
    }

    public function deleteThread(FeedbackThread $thread)
    {
        try {
            $referenceId = $thread->reference_id;

            // Delete all attachments
            foreach ($thread->messages as $message) {
                if ($message->hasAttachment()) {
                    $message->deleteAttachment();
                }
            }

            // Delete thread and messages (cascade)
            $thread->delete();

            // Log the deletion
            \Log::info('Feedback thread deleted', [
                'thread_id' => $thread->id,
                'reference_id' => $referenceId,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.feedback.index')
                ->with('success', "Thread {$referenceId} has been deleted.");

        } catch (\Exception $e) {
            \Log::error('Error deleting thread', [
                'thread_id' => $thread->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to delete thread. Please try again.']);
        }
    }

    public function export(Request $request)
    {
        try {
            $threads = FeedbackThread::with('messages')
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('date_from'), function ($query) use ($request) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->filled('date_to'), function ($query) use ($request) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = 'feedback-export-' . date('Y-m-d-H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($threads) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Reference ID',
                    'Visitor Name',
                    'Visitor Email',
                    'Subject',
                    'Status',
                    'Created At',
                    'Last Activity',
                    'Message Count',
                    'Has Attachments',
                ]);

                // Data rows
                foreach ($threads as $thread) {
                    fputcsv($file, [
                        $thread->reference_id,
                        $thread->visitor_name ?: 'Anonymous',
                        $thread->visitor_email,
                        $thread->subject,
                        $thread->status,
                        $thread->created_at->format('Y-m-d H:i:s'),
                        $thread->last_activity_at?->format('Y-m-d H:i:s'),
                        $thread->messages->count(),
                        $thread->messages->whereNotNull('attachment_path')->count() > 0 ? 'Yes' : 'No',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error exporting feedback', [
                'error' => $e->getMessage(),
            ]);
            
            return back()
                ->withErrors(['error' => 'Failed to export data. Please try again.']);
        }
    }
}