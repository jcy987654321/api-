<?php

namespace App\Services;

use App\Models\Feedback;
use App\Models\FeedbackReply;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    protected const CACHE_NOTIFICATION_COUNT_PREFIX = 'notification_count_';

    /**
     * 通知反馈回复
     */
    public function notifyFeedbackReply(Feedback $feedback, FeedbackReply $reply): void
    {
        // 如果反馈有用户ID且用户存在
        if ($feedback->user_id && $feedback->user) {
            $user = $feedback->user;
            $preference = NotificationPreference::getOrCreateForUser($user->id);

            // 创建系统通知
            if ($preference->notify_system) {
                $notification = $this->createNotification(
                    $user->id,
                    Notification::TYPE_FEEDBACK_REPLY,
                    $feedback->id,
                    'Your feedback has been replied',
                    'Your feedback "' . $feedback->title . '" has received a new reply from ' . $reply->name . '.'
                );

                // 清除缓存
                $this->clearUnreadCountCache($user->id);
            }

            // 发送邮件通知
            if ($preference->notify_email) {
                $this->sendEmailNotification($user, $notification ?? null, $feedback, $reply);
            }
        }
    }

    /**
     * 创建通知
     */
    public function createNotification(
        int $userId,
        string $type,
        ?int $relatedId,
        string $title,
        string $message
    ): Notification {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'related_id' => $relatedId,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);

        // 清除缓存
        $this->clearUnreadCountCache($userId);

        return $notification;
    }

    /**
     * 发送邮件通知
     */
    public function sendEmailNotification(User $user, ?Notification $notification, Feedback $feedback, FeedbackReply $reply): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\FeedbackReplyNotification($user, $feedback, $reply));
        } catch (\Exception $e) {
            \Log::error('Failed to send feedback reply email', [
                'user_id' => $user->id,
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 标记为已读
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $result = $notification->markAsRead();
        $this->clearUnreadCountCache($userId);

        return $result;
    }

    /**
     * 标记所有通知为已读
     */
    public function markAllAsRead(int $userId): int
    {
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->clearUnreadCountCache($userId);

        return $count;
    }

    /**
     * 获取用户通知
     */
    public function getUserNotifications(int $userId, int $limit = 20)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 获取未读通知数（带缓存）
     */
    public function getUnreadCount(int $userId): int
    {
        return Cache::remember(
            self::CACHE_NOTIFICATION_COUNT_PREFIX . $userId,
            60, // 缓存1分钟
            function () use ($userId) {
                return Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->count();
            }
        );
    }

    /**
     * 清除未读通知数缓存
     */
    public function clearUnreadCountCache(int $userId): void
    {
        Cache::forget(self::CACHE_NOTIFICATION_COUNT_PREFIX . $userId);
    }

    /**
     * 删除通知
     */
    public function delete(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $result = $notification->delete();
        $this->clearUnreadCountCache($userId);

        return $result;
    }

    /**
     * 发送系统消息通知
     */
    public function sendSystemMessage(int $userId, string $title, string $message): Notification
    {
        $notification = $this->createNotification(
            $userId,
            Notification::TYPE_SYSTEM_MESSAGE,
            null,
            $title,
            $message
        );

        $preference = NotificationPreference::getOrCreateForUser($userId);

        if ($preference->notify_email) {
            try {
                $user = User::find($userId);
                if ($user) {
                    Mail::to($user->email)->send(new \App\Mail\SystemNotification($title, $message));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send system notification email', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $notification;
    }
}
