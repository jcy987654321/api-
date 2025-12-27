<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->unsignedBigInteger('pv')->default(0)->comment('Page Views');
            $table->unsignedBigInteger('uv')->default(0)->comment('Unique Visitors');
            $table->unsignedBigInteger('spider_count')->default(0)->comment('Spider/Bot Visits');
            $table->timestamp('created_at')->useCurrent();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
