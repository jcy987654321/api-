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
            $table->string('reference_id')->unique();
            $table->string('visitor_name')->nullable();
            $table->text('visitor_email_encrypted');
            $table->string('subject_encrypted');
            $table->enum('status', ['open', 'closed', 'archived'])->default('open');
            $table->text('admin_notes_encrypted')->nullable();
            $table->ipAddress('visitor_ip');
            $table->string('visitor_user_agent')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_threads');
    }
};