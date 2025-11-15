<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_thread_id')->constrained()->onDelete('cascade');
            $table->enum('sender_type', ['visitor', 'admin']);
            $table->text('content_encrypted');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->string('attachment_mime_type')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->boolean('is_redacted')->default(false);
            $table->timestamp('sent_at');
            $table->timestamps();
            
            $table->index(['feedback_thread_id', 'sent_at']);
            $table->index('sender_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};