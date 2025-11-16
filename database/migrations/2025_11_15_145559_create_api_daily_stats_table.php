<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('total_calls')->default(0);
            $table->integer('successful_calls')->default(0);
            $table->integer('failed_calls')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->bigInteger('total_response_time')->default(0);
            $table->timestamps();
            
            $table->unique(['endpoint_id', 'date']);
            $table->index(['endpoint_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_daily_stats');
    }
};