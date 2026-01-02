<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_id')->comment('反馈ID');
            $table->unsignedBigInteger('user_id')->nullable()->comment('回复者ID');
            $table->string('name', 255)->comment('回复者名称');
            $table->text('content')->comment('回复内容');
            $table->boolean('is_admin')->default(false)->comment('是否为管理员回复');
            $table->json('attachments')->nullable()->comment('附件');
            $table->timestamps();

            $table->foreign('feedback_id')->references('id')->on('feedbacks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('feedback_id');
            $table->index('user_id');
            $table->index('is_admin');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_replies');
    }
};
