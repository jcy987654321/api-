<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FeedbackService
{
    protected const CACHE_STATS_PREFIX = 'feedback_stats_';

    /**
     * 提交反馈
     */
    public function submitFeedback(array $data): Feedback
    {
        $feedback = Feedback::create([
            'user_id' => $data['user_id'] ?? null,
            'tracking_code' => Feedback::generateTrackingCode(),
            'name' => $data['name'],
            'email' => $data['email'],
            'type' => $data['type'],
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => Feedback::STATUS_NEW,
            'priority' => $data['priority'] ?? Feedback::PRIORITY_MEDIUM,
            'attachments' => $data['attachments'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
        ]);

        // 清除统计缓存
        $this->clearStatsCache();

        return $feedback;
    }

    /**
     * 回复反馈
     */
    public function replyFeedback(int $feedbackId, array $data): FeedbackReply
    {
        $feedback = Feedback::findOrFail($feedbackId);

        $reply = FeedbackReply::create([
            'feedback_id' => $feedbackId,
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'content' => $data['content'],
            'is_admin' => $data['is_admin'] ?? false,
            'attachments' => $data['attachments'] ?? null,
        ]);

        // 更新反馈状态
        $feedback->update(['status' => Feedback::STATUS_REPLIED]);

        // 清除统计缓存
        $this->clearStatsCache();

        return $reply;
    }

    /**
     * 更新反馈状态
     */
    public function updateStatus(int $feedbackId, string $status): Feedback
    {
        $feedback = Feedback::findOrFail($feedbackId);

        $updateData = ['status' => $status];

        if ($status === Feedback::STATUS_RESOLVED) {
            $updateData['resolved_at'] = now();
        }

        $feedback->update($updateData);
        $this->clearStatsCache();

        return $feedback;
    }

    /**
     * 设置优先级
     */
    public function setPriority(int $feedbackId, string $priority): Feedback
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $feedback->update(['priority' => $priority]);
        $this->clearStatsCache();

        return $feedback;
    }

    /**
     * 指派给管理员
     */
    public function assignTo(int $feedbackId, int $userId): Feedback
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $feedback->update([
            'assigned_to' => $userId,
            'status' => Feedback::STATUS_REVIEWING,
        ]);
        $this->clearStatsCache();

        return $feedback;
    }

    /**
     * 获取反馈统计（带缓存）
     */
    public function getStats(): array
    {
        return Cache::remember(
            self::CACHE_STATS_PREFIX . 'main',
            300, // 缓存5分钟
            function () {
                return [
                    'total' => Feedback::count(),
                    'new' => Feedback::where('status', Feedback::STATUS_NEW)->count(),
                    'reviewing' => Feedback::where('status', Feedback::STATUS_REVIEWING)->count(),
                    'replied' => Feedback::where('status', Feedback::STATUS_REPLIED)->count(),
                    'resolved' => Feedback::where('status', Feedback::STATUS_RESOLVED)->count(),
                    'closed' => Feedback::where('status', Feedback::STATUS_CLOSED)->count(),
                    'by_type' => [
                        'bug' => Feedback::where('type', Feedback::TYPE_BUG)->count(),
                        'feature' => Feedback::where('type', Feedback::TYPE_FEATURE)->count(),
                        'suggestion' => Feedback::where('type', Feedback::TYPE_SUGGESTION)->count(),
                        'other' => Feedback::where('type', Feedback::TYPE_OTHER)->count(),
                    ],
                    'by_priority' => [
                        'low' => Feedback::where('priority', Feedback::PRIORITY_LOW)->count(),
                        'medium' => Feedback::where('priority', Feedback::PRIORITY_MEDIUM)->count(),
                        'high' => Feedback::where('priority', Feedback::PRIORITY_HIGH)->count(),
                    ],
                ];
            }
        );
    }

    /**
     * 获取用户反馈列表
     */
    public function getUserFeedbacks(int $userId, int $limit = 20)
    {
        return Feedback::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->with('replies')
            ->paginate($limit);
    }

    /**
     * 通过追踪码获取反馈
     */
    public function getByTrackingCode(string $trackingCode): ?Feedback
    {
        return Feedback::where('tracking_code', $trackingCode)
            ->with(['replies', 'assignedTo'])
            ->first();
    }

    /**
     * 导出反馈列表
     */
    public function export(array $filters = [])
    {
        $query = Feedback::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * 清除统计缓存
     */
    public function clearStatsCache(): void
    {
        Cache::forget(self::CACHE_STATS_PREFIX . 'main');
    }
}
