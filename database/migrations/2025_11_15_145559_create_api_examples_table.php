<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('request_example')->nullable();
            $table->text('response_example')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['endpoint_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_examples');
    }
};