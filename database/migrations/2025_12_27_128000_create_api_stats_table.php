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
        Schema::create('api_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_id')->constrained('apis')->onDelete('cascade');
            $table->unsignedBigInteger('call_count')->default(0);
            $table->timestamp('last_called_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique('api_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_stats');
    }
};
