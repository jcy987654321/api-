<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'mime_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_media_assets');
    }
};