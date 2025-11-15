<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('friend_links', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_url');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friend_links');
    }
};