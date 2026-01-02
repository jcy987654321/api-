<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 我的通知列表（需认证）
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->input('type');
        $readStatus = $request->input('read');

        $query = \App\Models\Notification::where('user_id', $user->id);

        if ($type) {
            $query->where('type', $type);
        }

        if ($readStatus === 'read') {
            $query->where('is_read', true);
        } elseif ($readStatus === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        return view('pages.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * 标记通知为已读
     */
    public function markAsRead($id)
    {
        $user = auth()->user();

        $result = $this->notificationService->markAsRead($id, $user->id);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Notification not found.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $this->notificationService->getUnreadCount($user->id),
            ]);
        }

        return redirect()->back();
    }

    /**
     * 标记全部通知为已读
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        $count = $this->notificationService->markAllAsRead($user->id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'marked_count' => $count,
            ]);
        }

        return redirect()->back()
            ->with('success', $count . ' notifications marked as read.');
    }

    /**
     * 删除通知
     */
    public function delete($id)
    {
        $user = auth()->user();

        $result = $this->notificationService->delete($id, $user->id);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Notification not found.');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $this->notificationService->getUnreadCount($user->id),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Notification deleted.');
    }

    /**
     * 通知偏好设置页面
     */
    public function preferences()
    {
        $user = auth()->user();
        $preference = NotificationPreference::getOrCreateForUser($user->id);

        return view('pages.notifications.preferences', compact('preference'));
    }

    /**
     * 更新通知偏好
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'notify_feedback_reply' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
            'notify_system' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $preference = NotificationPreference::getOrCreateForUser($user->id);

        $preference->update([
            'notify_feedback_reply' => $request->boolean('notify_feedback_reply'),
            'notify_email' => $request->boolean('notify_email'),
            'notify_system' => $request->boolean('notify_system'),
        ]);

        return redirect()->route('user.notification-preferences')
            ->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * 获取未读通知数（API）
     */
    public function unreadCount()
    {
        $user = auth()->user();

        return response()->json([
            'unread_count' => $this->notificationService->getUnreadCount($user->id),
        ]);
    }
}
