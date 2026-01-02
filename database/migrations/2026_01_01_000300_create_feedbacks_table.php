<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID（允许匿名）');
            $table->string('tracking_code', 32)->unique()->comment('追踪代码');
            $table->string('name', 255)->comment('反馈者名称');
            $table->string('email', 255)->comment('反馈者邮箱');
            $table->enum('type', ['bug', 'feature', 'suggestion', 'other'])->default('other')->comment('反馈类型');
            $table->string('title', 255)->comment('反馈标题');
            $table->text('content')->comment('反馈内容');
            $table->enum('status', ['new', 'reviewing', 'replied', 'resolved', 'closed'])->default('new')->comment('状态');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('优先级');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('指派管理员ID');
            $table->json('attachments')->nullable()->comment('附件URL');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->text('user_agent')->nullable()->comment('浏览器信息');
            $table->timestamp('resolved_at')->nullable()->comment('解决时间');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->index('tracking_code');
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('type');
            $table->index('assigned_to');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
