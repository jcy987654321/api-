<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackSubmitted;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Services\FeedbackService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    protected $feedbackService;
    protected $notificationService;

    public function __construct(FeedbackService $feedbackService, NotificationService $notificationService)
    {
        $this->feedbackService = $feedbackService;
        $this->notificationService = $notificationService;
    }

    /**
     * 显示反馈表单
     */
    public function showForm()
    {
        return view('pages.feedback.form', ['seoPage' => 'feedback']);
    }

    /**
     * 提交反馈
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => ['required', Rule::in(['bug', 'feature', 'suggestion', 'other'])],
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt|max:2048',
        ], [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email',
            'email.email' => 'Please enter a valid email address',
            'type.required' => 'Please select a feedback type',
            'type.in' => 'Invalid feedback type selected',
            'title.required' => 'Please enter a title',
            'content.required' => 'Please enter your feedback content',
            'content.min' => 'Feedback content must be at least 10 characters',
            'content.max' => 'Feedback content cannot exceed 5000 characters',
            'attachments.max' => 'Maximum 3 attachments allowed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 处理附件上传
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('feedback-attachments', 'public');
                    $attachments[] = $path;
                }
            }
        }

        $feedback = $this->feedbackService->submitFeedback([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'priority' => 'medium',
            'attachments' => !empty($attachments) ? $attachments : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 发送确认邮件
        try {
            Mail::to($feedback->email)->send(new FeedbackSubmitted($feedback));
        } catch (\Exception $e) {
            \Log::error('Failed to send feedback confirmation email', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('feedback.track', ['id' => $feedback->tracking_code])
            ->with('success', 'Thank you for your feedback! We have received it and will process it soon.');
    }

    /**
     * 追踪反馈状态
     */
    public function track($id)
    {
        $feedback = $this->feedbackService->getByTrackingCode($id);

        if (!$feedback) {
            return redirect()->route('feedback.form')
                ->with('error', 'Feedback not found. Please check your tracking code.');
        }

        return view('pages.feedback.track', compact('feedback'));
    }

    /**
     * 我的反馈列表（需认证）
     */
    public function myFeedbacks(Request $request)
    {
        $user = auth()->user();
        $status = $request->input('status');
        $type = $request->input('type');

        $query = Feedback::where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')
            ->with('replies')
            ->paginate(10);

        return view('pages.feedback.list', compact('feedbacks'));
    }

    /**
     * 反馈详情（需认证）
     */
    public function show($id)
    {
        $user = auth()->user();
        $feedback = Feedback::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['replies', 'assignedTo'])
            ->first();

        if (!$feedback) {
            return redirect()->route('user.feedbacks')
                ->with('error', 'Feedback not found.');
        }

        return view('pages.feedback.show', compact('feedback'));
    }

    /**
     * 用户回复反馈（需认证）
     */
    public function reply(Request $request, $id)
    {
        $user = auth()->user();
        $feedback = Feedback::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$feedback) {
            return redirect()->route('user.feedbacks')
                ->with('error', 'Feedback not found.');
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:5|max:2000',
            'attachments' => 'nullable|array|max:2',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt|max:2048',
        ], [
            'content.required' => 'Please enter your reply content',
            'content.min' => 'Reply must be at least 5 characters',
            'content.max' => 'Reply cannot exceed 2000 characters',
            'attachments.max' => 'Maximum 2 attachments allowed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 处理附件上传
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('feedback-attachments', 'public');
                    $attachments[] = $path;
                }
            }
        }

        $reply = $this->feedbackService->replyFeedback($id, [
            'user_id' => $user->id,
            'name' => $user->name,
            'content' => $request->input('content'),
            'is_admin' => false,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        // 如果有指派管理员，发送通知
        if ($feedback->assigned_to) {
            $this->notificationService->createNotification(
                $feedback->assigned_to,
                \App\Models\Notification::TYPE_FEEDBACK_REPLY,
                $feedback->id,
                'User replied to feedback',
                'User ' . $user->name . ' has replied to feedback: ' . $feedback->title
            );
        }

        return redirect()->route('feedbacks.show', ['id' => $id])
            ->with('success', 'Your reply has been submitted.');
    }
}
