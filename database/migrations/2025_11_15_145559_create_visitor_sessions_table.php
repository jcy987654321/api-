<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->string('landing_page')->nullable();
            $table->timestamp('last_activity');
            $table->timestamps();
            
            $table->index(['session_id', 'last_activity']);
            $table->index('last_activity');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_sessions');
    }
};