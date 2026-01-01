<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_key_id')->unique();
            $table->integer('count')->default(0);
            $table->timestamp('window_start_at');
            $table->timestamp('reset_at');
            $table->timestamps();
            
            $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('cascade');
            $table->index(['api_key_id', 'window_start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
    }
};