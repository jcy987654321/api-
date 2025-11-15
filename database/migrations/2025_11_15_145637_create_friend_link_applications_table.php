<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friend_link_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('feedback_thread_id')->nullable()->constrained()->onDelete('set null');
            $table->string('site_name');
            $table->string('site_url');
            $table->text('description');
            $table->binary('contact_encrypted')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('feedback_thread_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_link_applications');
    }
};