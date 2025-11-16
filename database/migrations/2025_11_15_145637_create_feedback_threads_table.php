<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'spam']);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->binary('contact_encrypted')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_threads');
    }
};