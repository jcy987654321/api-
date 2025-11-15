<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->timestamp('last_activity');
            $table->timestamps();
            
            $table->index(['user_id', 'last_activity']);
            $table->index('last_activity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};