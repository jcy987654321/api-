<?php

namespace Database\Seeders;

use App\Models\FeedbackMessage;
use App\Models\FeedbackThread;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample feedback threads
        $threads = [
            [
                'visitor_name' => '张三',
                'visitor_email' => 'zhangsan@qq.com',
                'subject' => '系统使用体验反馈',
                'message' => '这个系统非常好用，界面简洁，功能强大。希望能够增加更多的自定义选项，比如主题颜色设置等功能。',
                'status' => 'open',
                'created_at' => now()->subDays(2),
            ],
            [
                'visitor_name' => '李四',
                'visitor_email' => 'lisi@qq.com',
                'subject' => '功能建议',
                'message' => '建议在反馈系统中增加文件上传功能，这样用户可以上传截图来更好地描述问题。',
                'status' => 'closed',
                'created_at' => now()->subDays(5),
            ],
            [
                'visitor_name' => '王五',
                'visitor_email' => 'wangwu@qq.com',
                'subject' => 'Bug报告',
                'message' => '在移动端访问时，侧边栏的反馈按钮位置不太合适，建议调整一下位置。',
                'status' => 'open',
                'created_at' => now()->subDay(),
            ],
        ];

        foreach ($threads as $threadData) {
            $message = $threadData['message'];
            unset($threadData['message']);

            $thread = FeedbackThread::create([
                'visitor_name' => $threadData['visitor_name'],
                'visitor_email' => $threadData['visitor_email'],
                'subject' => $threadData['subject'],
                'status' => $threadData['status'],
                'visitor_ip' => '127.0.0.1',
                'visitor_user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => $threadData['created_at'],
                'updated_at' => $threadData['created_at'],
            ]);

            // Create initial message
            FeedbackMessage::create([
                'feedback_thread_id' => $thread->id,
                'sender_type' => 'visitor',
                'content' => $message,
                'sent_at' => $threadData['created_at'],
                'created_at' => $threadData['created_at'],
                'updated_at' => $threadData['created_at'],
            ]);

            // Add admin replies for some threads
            if ($thread->id % 2 == 0) {
                FeedbackMessage::create([
                    'feedback_thread_id' => $thread->id,
                    'sender_type' => 'admin',
                    'content' => '感谢您的反馈！我们会在下个版本中考虑您的建议。',
                    'sent_at' => $threadData['created_at']->addHours(2),
                    'created_at' => $threadData['created_at']->addHours(2),
                    'updated_at' => $threadData['created_at']->addHours(2),
                ]);
            }
        }
    }
}