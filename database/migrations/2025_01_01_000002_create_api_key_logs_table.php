<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_key_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_key_id');
            $table->string('method', 10);
            $table->string('endpoint', 255);
            $table->integer('status_code');
            $table->integer('response_time'); // milliseconds
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('cascade');
            $table->index('api_key_id');
            $table->index(['endpoint', 'method']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_key_logs');
    }
};