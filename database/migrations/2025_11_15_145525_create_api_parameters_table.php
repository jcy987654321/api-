<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'array', 'object']);
            $table->enum('location', ['query', 'path', 'header', 'cookie', 'body']);
            $table->text('description')->nullable();
            $table->boolean('required')->default(false);
            $table->string('default_value')->nullable();
            $table->text('validation_rules')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['endpoint_id', 'location', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_parameters');
    }
};