<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('method');
            $table->string('path');
            $table->text('description')->nullable();
            $table->text('response_format')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive', 'deprecated', 'draft'])->default('active');
            $table->bigInteger('hits_count')->default(0);
            $table->timestamp('last_called_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['category_id', 'status', 'sort_order']);
            $table->index(['method', 'path']);
            $table->index('last_called_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_endpoints');
    }
};