<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('visitor_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('remote_token_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_headers')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->timestamps();
            
            $table->index(['endpoint_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['visitor_session_id', 'created_at']);
            $table->index(['response_status', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_calls');
    }
};