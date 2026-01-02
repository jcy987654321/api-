<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FeedbackResolved;
use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\User;
use App\Services\FeedbackService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Writer;

class FeedbackManageController extends Controller
{
    protected $feedbackService;
    protected $notificationService;

    public function __construct(FeedbackService $feedbackService, NotificationService $notificationService)
    {
        $this->feedbackService = $feedbackService;
        $this->notificationService = $notificationService;
    }

    /**
     * 反馈列表
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $type = $request->input('type');
        $priority = $request->input('priority');
        $search = $request->input('search');

        $query = Feedback::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tracking_code', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')
            ->with(['user', 'assignedTo'])
            ->paginate(20);

        $stats = $this->feedbackService->getStats();

        return view('admin.feedbacks.index', compact('feedbacks', 'stats'));
    }

    /**
     * 反馈详情
     */
    public function show($id)
    {
        $feedback = Feedback::with(['user', 'assignedTo', 'replies.user'])->findOrFail($id);

        $admins = User::where('role', 'admin')->get();

        return view('admin.feedbacks.show', compact('feedback', 'admins'));
    }

    /**
     * 更新反馈状态
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(['new', 'reviewing', 'replied', 'resolved', 'closed'])],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $feedback = $this->feedbackService->updateStatus($id, $request->input('status'));

        // 如果标记为已解决，发送邮件通知
        if ($request->input('status') === 'resolved') {
            try {
                Mail::to($feedback->email)->send(new FeedbackResolved($feedback));
            } catch (\Exception $e) {
                \Log::error('Failed to send feedback resolved email', [
                    'feedback_id' => $feedback->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // 如果有用户ID，发送系统通知
            if ($feedback->user_id) {
                $this->notificationService->createNotification(
                    $feedback->user_id,
                    \App\Models\Notification::TYPE_FEEDBACK_REPLY,
                    $feedback->id,
                    'Your feedback has been resolved',
                    'Your feedback "' . $feedback->title . '" has been marked as resolved.'
                );
            }
        }

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }

    /**
     * 设置优先级
     */
    public function setPriority(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->feedbackService->setPriority($id, $request->input('priority'));

        return redirect()->back()
            ->with('success', 'Priority updated successfully.');
    }

    /**
     * 指派给管理员
     */
    public function assignTo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $feedback = $this->feedbackService->assignTo($id, $request->input('user_id'));

        return redirect()->back()
            ->with('success', 'Feedback assigned successfully.');
    }

    /**
     * 管理员回复反馈
     */
    public function reply(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();

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
            'user_id' => $admin->id,
            'name' => $admin->name,
            'content' => $request->input('content'),
            'is_admin' => true,
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        // 发送通知给反馈者
        $feedback = Feedback::findOrFail($id);
        if ($feedback->user_id) {
            $this->notificationService->notifyFeedbackReply($feedback, $reply);
        }

        return redirect()->back()
            ->with('success', 'Reply submitted successfully.');
    }

    /**
     * 删除反馈
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);

        // 删除相关的回复
        $feedback->replies()->delete();

        // 删除相关的通知
        $feedback->notifications()->delete();

        // 删除反馈
        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')
            ->with('success', 'Feedback deleted successfully.');
    }

    /**
     * 导出为 CSV
     */
    public function export(Request $request)
    {
        $filters = [
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'priority' => $request->input('priority'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $feedbacks = $this->feedbackService->export($filters);

        $csv = Writer::createFromString('');
        $csv->insertOne([
            'ID',
            'Tracking Code',
            'Name',
            'Email',
            'Type',
            'Title',
            'Status',
            'Priority',
            'Assigned To',
            'Created At',
            'Resolved At',
        ]);

        foreach ($feedbacks as $feedback) {
            $csv->insertOne([
                $feedback->id,
                $feedback->tracking_code,
                $feedback->name,
                $feedback->email,
                $feedback->type,
                $feedback->title,
                $feedback->status,
                $feedback->priority,
                $feedback->assignedTo ? $feedback->assignedTo->name : 'Unassigned',
                $feedback->created_at->format('Y-m-d H:i:s'),
                $feedback->resolved_at ? $feedback->resolved_at->format('Y-m-d H:i:s') : '',
            ]);
        }

        return response((string)$csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="feedbacks-' . date('Y-m-d') . '.csv"');
    }

    /**
     * 反馈统计
     */
    public function stats()
    {
        $stats = $this->feedbackService->getStats();

        // 获取最近7天的反馈趋势
        $trend = \App\Models\Feedback::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.feedbacks.stats', compact('stats', 'trend'));
    }
}
