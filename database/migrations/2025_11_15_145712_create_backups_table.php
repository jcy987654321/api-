<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->string('type');
            $table->enum('status', ['creating', 'completed', 'failed', 'deleted']);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};